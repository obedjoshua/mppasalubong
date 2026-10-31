<?php

namespace App\Http\Controllers;

use App\Models\ApiRequestMetric;
use App\Models\Post;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Throwable;

class PortalController extends Controller
{
    public function adminDashboard()
    {
        return view("portals.dashboard", $this->dashboardData("admin"));
    }

    public function developerDashboard()
    {
        return view("portals.dashboard", $this->dashboardData("developer"));
    }

    public function liveDashboard(): JsonResponse
    {
        return response()->json($this->liveDashboardData());
    }

    public function posts()
    {
        $posts = Post::with("user")->latest()->paginate(20);

        return view("portals.posts", compact("posts"));
    }

    public function apiRequests(Request $request)
    {
        $filters = $request->validate([
            "method" => ["nullable", "in:GET,POST,PUT,PATCH,DELETE"],
            "status" => ["nullable", "integer", "between:100,599"],
            "path" => ["nullable", "string", "max:100"],
        ]);

        $query = ApiRequestMetric::query()->latest();

        if (! empty($filters["method"])) {
            $query->where("method", $filters["method"]);
        }

        if (! empty($filters["status"])) {
            $query->where("status", $filters["status"]);
        }

        if (! empty($filters["path"])) {
            $query->where("path", "like", "%" . $filters["path"] . "%");
        }

        $requests = $query->paginate(50)->withQueryString();

        return view("portals.api-requests", compact("requests", "filters"));
    }

    public function updatePostVisibility(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            "moderation_status" => ["required", "in:published,hidden"],
        ]);

        $post->moderation_status = $validated["moderation_status"];
        $post->save();

        return back()->with("success", "Post visibility updated.");
    }

    public function users()
    {
        $users = User::query()->orderBy("name")->paginate(30);

        return view("portals.users", compact("users"));
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            "role" => ["required", "in:customer,staff,developer,admin"],
        ]);

        if ($user->is($request->user()) && ! in_array($validated["role"], ["admin", "developer"], true)) {
            return back()->withErrors(["role" => "You cannot remove your own administrator or developer access."]);
        }

        if (in_array($user->role, ["admin", "developer"], true)
            && ! in_array($validated["role"], ["admin", "developer"], true)
            && User::whereIn("role", ["admin", "developer"])->count() <= 1) {
            return back()->withErrors(["role" => "The last administrator or developer cannot be demoted."]);
        }

        $user->role = $validated["role"];
        $user->save();

        return back()->with("success", "User role updated.");
    }

    public function purchases()
    {
        $purchases = Purchase::with(["user", "product"])->latest()->paginate(20);

        return view("portals.purchases", compact("purchases"));
    }

    public function showPurchase(Purchase $purchase)
    {
        $purchase->load(["user", "product"]);

        return view("portals.purchase-detail", compact("purchase"));
    }

    public function updatePurchaseStatus(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            "status" => ["required", "in:pending,completed,cancelled"],
        ]);

        $purchase->status = $validated["status"];
        $purchase->save();

        return back()->with("success", "Purchase status updated.");
    }

    private function dashboardData(string $portal): array
    {
        $liveData = $this->liveDashboardData();

        $connections = [
            "database" => $this->checkDatabase(),
            "cache" => $this->checkCache(),
        ];

        return [
            "portal" => $portal,
            "liveData" => $liveData,
            "userCounts" => [
                "all" => User::count(),
                "customers" => User::where("role", "customer")->count(),
                "staff" => User::where("role", "staff")->count(),
                "developers" => User::where("role", "developer")->count(),
                "admins" => User::where("role", "admin")->count(),
            ],
            "apiStats" => $liveData["stats"],
            "traffic" => $liveData["traffic"],
            "pendingPosts" => Post::where("moderation_status", "hidden")->count(),
            "connections" => $connections,
            "gitStatus" => $this->gitStatus(),
        ];
    }

    private function liveDashboardData(): array
    {
        $since = now()->subDay();
        $metrics = ApiRequestMetric::where("created_at", ">=", $since);
        $hourExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => 'strftime("%Y-%m-%d %H:00", created_at)',
            'pgsql' => 'to_char(date_trunc(\'hour\', created_at), \'YYYY-MM-DD HH24:00\')',
            default => 'DATE_FORMAT(created_at, "%Y-%m-%d %H:00")',
        };
        $hourlyCounts = (clone $metrics)
            ->selectRaw("$hourExpression as hour, COUNT(*) as total")
            ->groupBy("hour")
            ->pluck("total", "hour");
        $traffic = collect(range(23, 0))->map(function (int $hoursAgo) use ($hourlyCounts) {
            $hour = now()->startOfHour()->subHours($hoursAgo);
            $key = $hour->format("Y-m-d H:00");

            return [
                "label" => $hour->format("H:i"),
                "count" => (int) $hourlyCounts->get($key, 0),
            ];
        })->values();

        return [
            "updatedAt" => now()->toIso8601String(),
            "stats" => [
                "total" => (clone $metrics)->count(),
                "successful" => (clone $metrics)->whereBetween("status", [200, 399])->count(),
                "clientErrors" => (clone $metrics)->whereBetween("status", [400, 499])->count(),
                "serverErrors" => (clone $metrics)->whereBetween("status", [500, 599])->count(),
                "averageDuration" => (int) round((clone $metrics)->avg("duration_ms") ?? 0),
            ],
            "traffic" => $traffic,
            "feed" => (clone $metrics)->latest()->limit(8)->get()->map(fn (ApiRequestMetric $metric) => [
                "time" => $metric->created_at?->format("H:i:s"),
                "method" => $metric->method,
                "path" => $metric->path,
                "status" => $metric->status,
                "duration" => $metric->duration_ms,
            ]),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            DB::select("SELECT 1");

            return ["healthy" => true, "detail" => "Database query succeeded"];
        } catch (Throwable) {
            return ["healthy" => false, "detail" => "Database query failed"];
        }
    }

    private function checkCache(): array
    {
        $key = "portal-health-check-" . bin2hex(random_bytes(8));

        try {
            Cache::put($key, "ok", 10);
            $healthy = Cache::get($key) === "ok";
            Cache::forget($key);

            return [
                "healthy" => $healthy,
                "detail" => $healthy ? "Cache read/write succeeded" : "Cache read/write failed",
            ];
        } catch (Throwable) {
            return ["healthy" => false, "detail" => "Cache connection failed"];
        } finally {
            try {
                Cache::forget($key);
            } catch (Throwable) {
                // Cache cleanup is best effort.
            }
        }
    }

    private function gitStatus(): array
    {
        try {
            $process = new Process(["git", "-C", base_path(), "status", "--short", "--branch"]);
            $process->setTimeout(3);
            $process->run();

            return [
                "available" => $process->isSuccessful(),
                "output" => $process->isSuccessful()
                    ? (trim($process->getOutput()) ?: "Working tree clean")
                    : "Git status is unavailable in this environment.",
            ];
        } catch (Throwable) {
            return ["available" => false, "output" => "Git status is unavailable in this environment."];
        }
    }
}
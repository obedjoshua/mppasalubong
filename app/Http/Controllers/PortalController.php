<?php

namespace App\Http\Controllers;

use App\Models\ApiRequestMetric;
use App\Models\Notification;
use App\Models\OrderTicket;
use App\Models\Post;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function users(Request $request)
    {
        $filters = $request->validate([
            "q"    => ["nullable", "string", "max:100"],
            "role" => ["nullable", "in:all,customer,staff,developer,admin"],
            "sort" => ["nullable", "in:name_asc,name_desc,role_asc,role_desc,newest"],
        ]);

        $query = User::query();

        if (! empty($filters["q"])) {
            $search = $filters["q"];
            $query->where(function ($sub) use ($search): void {
                $sub->where("name", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%");
            });
        }

        if (! empty($filters["role"]) && $filters["role"] !== "all") {
            $query->where("role", $filters["role"]);
        }

        match ($filters["sort"] ?? "name_asc") {
            "name_desc" => $query->orderByDesc("name"),
            "role_asc"  => $query->orderBy("role")->orderBy("name"),
            "role_desc" => $query->orderByDesc("role")->orderBy("name"),
            "newest"    => $query->latest(),
            default     => $query->orderBy("name"),
        };

        $users = $query->paginate(30)->withQueryString();

        $roleCounts = [
            "all"       => User::count(),
            "customer"  => User::where("role", "customer")->count(),
            "staff"     => User::where("role", "staff")->count(),
            "developer" => User::where("role", "developer")->count(),
            "admin"     => User::where("role", "admin")->count(),
        ];

        return view("portals.users", compact("users", "filters", "roleCounts"));
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
        $relations = array_values(array_filter(
            ["user", "product"],
            fn (string $relation): bool => method_exists(Purchase::class, $relation)
        ));

        $query = Purchase::query()->latest();
        if (! empty($relations)) {
            $query->with($relations);
        }

        $purchases = $query->paginate(20);

        return view("portals.purchases", compact("purchases"));
    }

    public function showPurchase(Purchase $purchase)
    {
        $relations = array_values(array_filter(
            ["user", "product"],
            fn (string $relation): bool => method_exists($purchase, $relation)
        ));

        if (! empty($relations)) {
            $purchase->load($relations);
        }

        return view("portals.purchase-detail", compact("purchase"));
    }

    public function updatePurchaseStatus(Request $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validate([
            "status" => ["required", "in:pending,completed,cancelled"],
        ]);

        $purchase->status = $validated["status"];
        $purchase->save();

        return back()->with("success", "Purchase status updated.");
    }

    /**
     * Show the Promotional Broadcast Creation Form
     */
    public function createPromotionalBroadcast()
    {
        return view("portals.create-promotion");
    }

    /**
     * Dispatch Promotional Broadcast via DB Notification to All Customers
     */
    public function sendPromotionalBroadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle uploaded banner image or select fallback
        $bannerUrl = asset('images/community_header_01.png');
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('promotions', 'public');
            $bannerUrl = Storage::url((string) $path);
        }

        // Restrict delivery to customers only
        $customers = User::where('role', 'customer')->get();

        // Dispatch notification with synced title, message, and banner URL
        foreach ($customers as $customer) {
            Notification::create([
                'user_id'      => $customer->id,
                'title'        => $validated['title'],
                'message'      => $validated['description'],
                'type'         => 'promotional',
                'banner_image' => $bannerUrl,
                'is_read'      => false,
                'data'         => [
                    'title'        => $validated['title'],
                    'message'      => $validated['description'],
                    'body'         => $validated['description'],
                    'type'         => 'promotional',
                    'banner_image' => $bannerUrl,
                    'action_url'   => route('notifications.index'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Promotional broadcast successfully sent to all customers!');
    }

    private function dashboardData(string $portal): array
    {
        $liveData = $this->liveDashboardData();

        $connections = [
            "database" => $this->checkDatabase(),
            "cache" => $this->checkCache(),
        ];

        $calendarOrders = OrderTicket::with(['user', 'items'])
            ->whereNotNull('pickup_date')
            ->orderBy('pickup_time')
            ->get()
            ->groupBy(fn (OrderTicket $ticket): string => Carbon::parse($ticket->pickup_date)->format('Y-m-d'))
            ->map(function ($tickets) {
                return $tickets->map(function (OrderTicket $ticket): array {
                    $itemsSummary = $ticket->items
                        ->pluck('item_type')
                        ->map(fn ($type): string => ucfirst(str_replace('_', ' ', (string) $type)))
                        ->join(', ');

                    $customerName = $ticket->user !== null
                        ? $ticket->user->name
                        : ((string) ($ticket->getAttribute('customer_name') ?? 'Guest Customer'));

                    $amount = (float) ($ticket->getAttribute('total_price') ?? $ticket->getAttribute('quoted_price') ?? 0);

                    return [
                        'id'          => $ticket->id,
                        'customer'    => $customerName,
                        'items'       => $itemsSummary !== '' ? $itemsSummary : 'Custom Order',
                        'pickup_time' => (string) ($ticket->pickup_time ?? 'TBD'),
                        'status'      => ucwords(str_replace('_', ' ', (string) ($ticket->status ?? 'pending'))),
                        'total'       => number_format($amount, 2),
                        'url'         => route('staff.tickets.show', $ticket->id),
                    ];
                })->values();
            });

        return [
            "portal" => $portal,
            "liveData" => $liveData,
            "calendarOrders" => $calendarOrders,
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

        // 24-hour rolling buckets
        $hourlyCounts = (clone $metrics)
            ->selectRaw("$hourExpression as hour, COUNT(*) as total")
            ->groupBy("hour")
            ->pluck("total", "hour");

        $traffic = collect(range(23, 0))->map(function (int $hoursAgo) use ($hourlyCounts): array {
            $hour = now()->startOfHour()->subHours($hoursAgo);
            $key = $hour->format("Y-m-d H:00");

            return [
                "timestamp" => $hour->toIso8601String(),
                "label" => $hour->format("H:i"),
                "count" => (int) $hourlyCounts->get($key, 0),
            ];
        })->values();

        // Persistent lifetime hourly history (never wiped across 15s refreshes)
        $lifetimeHistory = ApiRequestMetric::query()
            ->selectRaw("$hourExpression as hour, COUNT(*) as total")
            ->groupBy("hour")
            ->orderBy("hour")
            ->get()
            ->map(function ($row): array {
                /** @var object{hour: string, total: int|string} $row */
                $parsed = Carbon::parse($row->hour);

                return [
                    "timestamp" => $parsed->toIso8601String(),
                    "label" => $parsed->format("M j, H:i"),
                    "count" => (int) $row->total,
                ];
            })
            ->values();

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
            "history" => $lifetimeHistory,
            "feed" => (clone $metrics)->latest()->limit(8)->get()->map(fn (ApiRequestMetric $metric): array => [
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

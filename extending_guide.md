# Hướng dẫn mở rộng Manga CMS Backend

Tài liệu này cung cấp hướng dẫn chi tiết về cách thêm một tính năng mới vào Manga CMS Backend. Chúng tôi sẽ đi qua từng bước với ví dụ cụ thể để giúp bạn hiểu rõ quy trình phát triển theo Domain-Driven Design (DDD).

## Quy trình thêm tính năng mới

### 1. Xác định yêu cầu nghiệp vụ

Trước khi bắt đầu viết code, hãy xác định rõ:
- Tính năng này giải quyết vấn đề gì?
- Nó thuộc về domain nào trong hệ thống?
- Các thực thể và mối quan hệ liên quan?
- Các use cases (trường hợp sử dụng) cần triển khai?

### 2. Thiết kế Domain Model

Sau khi hiểu rõ yêu cầu, hãy thiết kế domain model:

1. **Tạo Entity (Thực thể)**: Định nghĩa các thuộc tính và phương thức
2. **Định nghĩa Repository Interface**: Xác định các phương thức truy xuất dữ liệu
3. **Tạo Data Transfer Objects (DTOs)**: Đóng gói dữ liệu truyền giữa các layer
4. **Định nghĩa Events**: Xác định các sự kiện domain cần phát sinh

### 3. Triển khai Infrastructure Layer

Triển khai các repository và services:

1. **Repository Implementation**: Triển khai repository interface
2. **Migrations**: Tạo migration cho database schema
3. **Eloquent Model**: Tạo Eloquent model nếu cần

### 4. Triển khai Application Layer

Triển khai các use cases:

1. **Actions**: Triển khai các actions xử lý logic nghiệp vụ
2. **Event Handlers**: Triển khai xử lý các events

### 5. Triển khai Presentation Layer

Triển khai giao diện API:

1. **Controllers**: Xử lý HTTP requests
2. **Requests**: Xác thực và validate input
3. **Resources**: Định dạng response
4. **Routes**: Định nghĩa API endpoints

### 6. Triển khai Authorization

Cập nhật hệ thống phân quyền:

1. **Policies**: Định nghĩa quyền truy cập
2. **Middleware**: Cập nhật middleware nếu cần

### 7. Kiểm thử

Viết tests cho tính năng mới:

1. **Unit Tests**: Kiểm thử các components riêng lẻ
2. **Feature Tests**: Kiểm thử tính năng end-to-end
3. **Integration Tests**: Kiểm thử tương tác giữa các components

### 8. Cập nhật tài liệu

Cập nhật tài liệu API và implementation:

1. **API Documentation**: Thêm endpoints mới
2. **Implementation Documentation**: Mô tả tính năng mới

## Ví dụ: Thêm tính năng "Nhóm dịch" (Translation Team)

Giả sử chúng ta muốn thêm tính năng quản lý nhóm dịch, cho phép nhiều người dùng cùng làm việc trên một dự án dịch manga.

### 1. Xác định yêu cầu nghiệp vụ

- **Vấn đề**: Cần quản lý nhóm dịch và phân công công việc
- **Domain**: TranslationTeam
- **Thực thể**: Team, TeamMember, TeamProject
- **Use cases**: Tạo nhóm, thêm thành viên, phân công dự án, v.v.

### 2. Thiết kế Domain Model

#### Tạo Entity

```php
<?php

namespace Domain\TranslationTeam\Models;

/**
 * Domain Model for Team
 */
class Team
{
    private int $id;
    private string $name;
    private string $slug;
    private ?string $description;
    private ?string $logo;
    private int $leaderId;
    private array $members;
    private array $projects;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private ?\DateTimeInterface $deletedAt;

    /**
     * Create a new Team instance
     */
    public function __construct(
        int $id,
        string $name,
        string $slug,
        int $leaderId,
        ?string $description = null,
        ?string $logo = null,
        array $members = [],
        array $projects = [],
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null,
        ?\DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->leaderId = $leaderId;
        $this->description = $description;
        $this->logo = $logo;
        $this->members = $members;
        $this->projects = $projects;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    // Getters and setters...

    /**
     * Add a member to the team
     */
    public function addMember(int $userId, string $role = 'member'): self
    {
        $this->members[] = [
            'user_id' => $userId,
            'role' => $role
        ];
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Remove a member from the team
     */
    public function removeMember(int $userId): self
    {
        $this->members = array_filter($this->members, function ($member) use ($userId) {
            return $member['user_id'] !== $userId;
        });
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Add a project to the team
     */
    public function addProject(int $mangaId): self
    {
        $this->projects[] = $mangaId;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Remove a project from the team
     */
    public function removeProject(int $mangaId): self
    {
        $this->projects = array_filter($this->projects, function ($project) use ($mangaId) {
            return $project !== $mangaId;
        });
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
```

#### Định nghĩa Repository Interface

```php
<?php

namespace Domain\TranslationTeam\Repositories;

use Domain\TranslationTeam\Models\Team;

/**
 * Interface for Team Repository
 */
interface TeamRepositoryInterface
{
    /**
     * Find team by ID
     */
    public function findById(int $id): ?Team;
    
    /**
     * Find team by slug
     */
    public function findBySlug(string $slug): ?Team;
    
    /**
     * Get all teams with pagination
     */
    public function getAll(int $page = 1, int $perPage = 15): array;
    
    /**
     * Get teams by user ID
     */
    public function getByUserId(int $userId, int $page = 1, int $perPage = 15): array;
    
    /**
     * Get teams by manga ID
     */
    public function getByMangaId(int $mangaId, int $page = 1, int $perPage = 15): array;
    
    /**
     * Save team (create or update)
     */
    public function save(Team $team): Team;
    
    /**
     * Delete team
     */
    public function delete(int $id): bool;
    
    /**
     * Restore deleted team
     */
    public function restore(int $id): bool;
    
    /**
     * Add member to team
     */
    public function addMember(int $teamId, int $userId, string $role = 'member'): bool;
    
    /**
     * Remove member from team
     */
    public function removeMember(int $teamId, int $userId): bool;
    
    /**
     * Add project to team
     */
    public function addProject(int $teamId, int $mangaId): bool;
    
    /**
     * Remove project from team
     */
    public function removeProject(int $teamId, int $mangaId): bool;
}
```

#### Tạo Data Transfer Objects

```php
<?php

namespace Domain\TranslationTeam\DataTransferObjects;

/**
 * Data Transfer Object for Team
 */
class TeamData
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $name = '',
        public readonly ?string $slug = null,
        public readonly int $leaderId = 0,
        public readonly ?string $description = null,
        public readonly ?string $logo = null,
        public readonly array $members = [],
        public readonly array $projects = []
    ) {
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? null,
            leaderId: $data['leader_id'] ?? 0,
            description: $data['description'] ?? null,
            logo: $data['logo'] ?? null,
            members: $data['members'] ?? [],
            projects: $data['projects'] ?? []
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'leader_id' => $this->leaderId,
            'description' => $this->description,
            'logo' => $this->logo,
            'members' => $this->members,
            'projects' => $this->projects
        ];
    }
}
```

#### Định nghĩa Events

```php
<?php

namespace Domain\TranslationTeam\Events;

use Domain\TranslationTeam\Models\Team;

/**
 * Event triggered when a team is created
 */
class TeamCreated
{
    /**
     * @param Team $team
     */
    public function __construct(
        public readonly Team $team
    ) {
    }
}
```

### 3. Triển khai Infrastructure Layer

#### Repository Implementation

```php
<?php

namespace Infrastructure\Repositories;

use App\Models\Team as EloquentTeam;
use Domain\TranslationTeam\Models\Team;
use Domain\TranslationTeam\Repositories\TeamRepositoryInterface;

/**
 * Eloquent implementation of TeamRepositoryInterface
 */
class EloquentTeamRepository implements TeamRepositoryInterface
{
    /**
     * Convert Eloquent model to Domain model
     */
    private function toDomainModel(EloquentTeam $eloquentTeam): Team
    {
        return new Team(
            id: $eloquentTeam->id,
            name: $eloquentTeam->name,
            slug: $eloquentTeam->slug,
            leaderId: $eloquentTeam->leader_id,
            description: $eloquentTeam->description,
            logo: $eloquentTeam->logo,
            members: $eloquentTeam->members->map(function ($member) {
                return [
                    'user_id' => $member->user_id,
                    'role' => $member->role
                ];
            })->toArray(),
            projects: $eloquentTeam->projects->pluck('id')->toArray(),
            createdAt: $eloquentTeam->created_at,
            updatedAt: $eloquentTeam->updated_at,
            deletedAt: $eloquentTeam->deleted_at
        );
    }

    /**
     * Find team by ID
     */
    public function findById(int $id): ?Team
    {
        $eloquentTeam = EloquentTeam::with(['members', 'projects'])->find($id);
        
        if (!$eloquentTeam) {
            return null;
        }
        
        return $this->toDomainModel($eloquentTeam);
    }
    
    /**
     * Find team by slug
     */
    public function findBySlug(string $slug): ?Team
    {
        $eloquentTeam = EloquentTeam::with(['members', 'projects'])->where('slug', $slug)->first();
        
        if (!$eloquentTeam) {
            return null;
        }
        
        return $this->toDomainModel($eloquentTeam);
    }
    
    /**
     * Get all teams with pagination
     */
    public function getAll(int $page = 1, int $perPage = 15): array
    {
        $paginator = EloquentTeam::paginate($perPage, ['*'], 'page', $page);
        
        $teams = $paginator->map(function ($eloquentTeam) {
            return $this->toDomainModel($eloquentTeam);
        })->all();
        
        return [
            'data' => $teams,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    // Các phương thức khác...
}
```

#### Migrations

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('leader_id')->constrained('users');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member');
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('manga_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manga_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['manga_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manga_team');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }
};
```

#### Eloquent Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'leader_id',
        'description',
        'logo',
    ];

    /**
     * Get the leader of the team.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get the members of the team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the projects of the team.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'manga_team')
            ->withTimestamps();
    }
}
```

### 4. Triển khai Application Layer

#### Actions

```php
<?php

namespace Domain\TranslationTeam\Actions;

use Domain\TranslationTeam\DataTransferObjects\TeamData;
use Domain\TranslationTeam\Models\Team;
use Domain\TranslationTeam\Repositories\TeamRepositoryInterface;
use Domain\TranslationTeam\Events\TeamCreated;
use Domain\User\Repositories\UserRepositoryInterface;

/**
 * Action to create a new team
 */
class CreateTeamAction
{
    /**
     * @param TeamRepositoryInterface $teamRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Execute the action
     *
     * @param TeamData $teamData
     * @return Team
     * @throws \Exception
     */
    public function execute(TeamData $teamData): Team
    {
        // Verify leader exists
        $leader = $this->userRepository->findById($teamData->leaderId);
        if (!$leader) {
            throw new \Exception("User with ID {$teamData->leaderId} not found");
        }

        // Generate slug if not provided
        $slug = $teamData->slug ?? $this->generateSlug($teamData->name);

        // Create a new Team domain model
        $team = new Team(
            id: 0, // Temporary ID, will be replaced by repository
            name: $teamData->name,
            slug: $slug,
            leaderId: $teamData->leaderId,
            description: $teamData->description,
            logo: $teamData->logo,
            members: $teamData->members,
            projects: $teamData->projects
        );

        // Save the team using repository
        $savedTeam = $this->teamRepository->save($team);

        // Add leader as a member with role 'leader'
        $this->teamRepository->addMember($savedTeam->getId(), $teamData->leaderId, 'leader');

        // Add other members
        foreach ($teamData->members as $member) {
            if ($member['user_id'] !== $teamData->leaderId) {
                $this->teamRepository->addMember(
                    $savedTeam->getId(),
                    $member['user_id'],
                    $member['role'] ?? 'member'
                );
            }
        }

        // Add projects
        foreach ($teamData->projects as $mangaId) {
            $this->teamRepository->addProject($savedTeam->getId(), $mangaId);
        }

        // Dispatch event
        event(new TeamCreated($savedTeam));

        return $savedTeam;
    }

    /**
     * Generate a slug from name
     *
     * @param string $name
     * @return string
     */
    private function generateSlug(string $name): string
    {
        // Convert to lowercase
        $slug = strtolower($name);
        
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
```

### 5. Triển khai Presentation Layer

#### Controllers

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\Team\TeamResource;
use App\Http\Resources\Team\TeamCollection;
use Domain\TranslationTeam\Actions\CreateTeamAction;
use Domain\TranslationTeam\Actions\UpdateTeamAction;
use Domain\TranslationTeam\Actions\DeleteTeamAction;
use Domain\TranslationTeam\DataTransferObjects\TeamData;
use Domain\TranslationTeam\Repositories\TeamRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    /**
     * @param TeamRepositoryInterface $teamRepository
     */
    public function __construct(
        private TeamRepositoryInterface $teamRepository
    ) {
    }

    /**
     * Display a listing of teams.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $result = $this->teamRepository->getAll($page, $perPage);

        return response()->json([
            'data' => TeamCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }

    /**
     * Store a newly created team.
     *
     * @param StoreTeamRequest $request
     * @param CreateTeamAction $createTeamAction
     * @return JsonResponse
     */
    public function store(StoreTeamRequest $request, CreateTeamAction $createTeamAction): JsonResponse
    {
        $teamData = TeamData::fromArray($request->validated());

        try {
            $team = $createTeamAction->execute($teamData);

            return response()->json([
                'message' => 'Team created successfully',
                'data' => new TeamResource($team)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified team.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $team = $this->teamRepository->findBySlug($slug);

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], 404);
        }

        return response()->json([
            'data' => new TeamResource($team)
        ]);
    }

    // Các phương thức khác...
}
```

#### Requests

```php
<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:teams,slug',
            'leader_id' => 'required|integer|exists:users,id',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'members' => 'nullable|array',
            'members.*.user_id' => 'required|integer|exists:users,id',
            'members.*.role' => 'required|string|in:leader,translator,editor,proofreader,member',
            'projects' => 'nullable|array',
            'projects.*' => 'required|integer|exists:mangas,id'
        ];
    }
}
```

#### Resources

```php
<?php

namespace App\Http\Resources\Team;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Manga\MangaResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'description' => $this->getDescription(),
            'logo' => $this->getLogo(),
            'leader' => new UserResource($this->getLeader()),
            'members' => $this->getMembers(),
            'projects' => $this->getProjects(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}
```

#### Routes

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;

// Public routes
Route::prefix('v1')->group(function () {
    // Team routes
    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/teams/{slug}', [TeamController::class, 'show']);
    Route::get('/teams/{slug}/members', [TeamController::class, 'members']);
    Route::get('/teams/{slug}/projects', [TeamController::class, 'projects']);
});

// Protected routes
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Team management
    Route::middleware(['check.role:admin,mod,translator'])->group(function () {
        Route::post('/teams', [TeamController::class, 'store']);
        Route::put('/teams/{slug}', [TeamController::class, 'update']);
        Route::delete('/teams/{slug}', [TeamController::class, 'destroy']);
        Route::post('/teams/{slug}/members', [TeamController::class, 'addMember']);
        Route::delete('/teams/{slug}/members/{userId}', [TeamController::class, 'removeMember']);
        Route::post('/teams/{slug}/projects', [TeamController::class, 'addProject']);
        Route::delete('/teams/{slug}/projects/{mangaId}', [TeamController::class, 'removeProject']);
    });
});
```

### 6. Triển khai Authorization

#### Policies

```php
<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view team listings
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Team $team): bool
    {
        // Anyone can view teams
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin, mod, or translator can create teams
        return in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        // Team leader can update team
        if ($team->leader_id === $user->id) {
            return true;
        }

        // Admin and mod can update any team
        return in_array($user->role, ['admin', 'mod']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        // Only admin or team leader can delete team
        return $user->role === 'admin' || $team->leader_id === $user->id;
    }

    /**
     * Determine whether the user can manage members.
     */
    public function manageMembers(User $user, Team $team): bool
    {
        // Team leader can manage members
        if ($team->leader_id === $user->id) {
            return true;
        }

        // Admin and mod can manage members
        return in_array($user->role, ['admin', 'mod']);
    }

    /**
     * Determine whether the user can manage projects.
     */
    public function manageProjects(User $user, Team $team): bool
    {
        // Team leader can manage projects
        if ($team->leader_id === $user->id) {
            return true;
        }

        // Admin and mod can manage projects
        return in_array($user->role, ['admin', 'mod']);
    }
}
```

### 7. Cập nhật Service Provider

Đăng ký repository interface và implementation trong service provider:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Domain\TranslationTeam\Repositories\TeamRepositoryInterface;
use Infrastructure\Repositories\EloquentTeamRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repositories
        $this->app->bind(TeamRepositoryInterface::class, EloquentTeamRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

### 8. Cập nhật tài liệu API

Thêm các endpoints mới vào tài liệu API:

```markdown
## Teams

### List Teams

**Endpoint:** `GET /teams`

**Query Parameters:**
```
page: integer (default: 1)
per_page: integer (default: 15)
```

**Response:**
```json
{
  "data": [
    {
      "id": "integer",
      "name": "string",
      "slug": "string",
      "description": "string|null",
      "logo": "string|null",
      "leader": {
        "id": "integer",
        "username": "string",
        "avatar": "string|null"
      },
      "members": [
        {
          "user_id": "integer",
          "username": "string",
          "role": "string",
          "avatar": "string|null"
        }
      ],
      "projects": [
        {
          "id": "integer",
          "title": "string",
          "slug": "string",
          "thumbnail": "string|null"
        }
      ],
      "created_at": "datetime",
      "updated_at": "datetime"
    }
  ],
  "meta": {
    "total": "integer",
    "per_page": "integer",
    "current_page": "integer",
    "last_page": "integer"
  }
}
```

### Get Team

**Endpoint:** `GET /teams/{slug}`

**Response:**
```json
{
  "data": {
    "id": "integer",
    "name": "string",
    "slug": "string",
    "description": "string|null",
    "logo": "string|null",
    "leader": {
      "id": "integer",
      "username": "string",
      "avatar": "string|null"
    },
    "members": [
      {
        "user_id": "integer",
        "username": "string",
        "role": "string",
        "avatar": "string|null"
      }
    ],
    "projects": [
      {
        "id": "integer",
        "title": "string",
        "slug": "string",
        "thumbnail": "string|null"
      }
    ],
    "created_at": "datetime",
    "updated_at": "datetime"
  }
}
```

### Create Team

**Endpoint:** `POST /teams`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```
name: string
slug: string (optional)
leader_id: integer
description: string (optional)
logo: file (optional)
members: array (optional)
projects: array (optional)
```

**Response:**
```json
{
  "message": "Team created successfully",
  "data": {
    "id": "integer",
    "name": "string",
    "slug": "string",
    "description": "string|null",
    "logo": "string|null",
    "leader": {
      "id": "integer",
      "username": "string",
      "avatar": "string|null"
    },
    "members": [
      {
        "user_id": "integer",
        "username": "string",
        "role": "string",
        "avatar": "string|null"
      }
    ],
    "projects": [
      {
        "id": "integer",
        "title": "string",
        "slug": "string",
        "thumbnail": "string|null"
      }
    ],
    "created_at": "datetime",
    "updated_at": "datetime"
  }
}
```

// Các endpoints khác...
```

## Tổng kết

Quy trình thêm tính năng mới vào Manga CMS Backend theo Domain-Driven Design bao gồm các bước:

1. **Xác định yêu cầu nghiệp vụ**: Hiểu rõ vấn đề cần giải quyết
2. **Thiết kế Domain Model**: Tạo entities, repository interfaces, DTOs, và events
3. **Triển khai Infrastructure Layer**: Repository implementations, migrations, và Eloquent models
4. **Triển khai Application Layer**: Actions và event handlers
5. **Triển khai Presentation Layer**: Controllers, requests, resources, và routes
6. **Triển khai Authorization**: Policies và middleware
7. **Kiểm thử**: Unit tests, feature tests, và integration tests
8. **Cập nhật tài liệu**: API documentation và implementation documentation

Bằng cách tuân theo quy trình này, bạn có thể dễ dàng thêm các tính năng mới vào Manga CMS Backend mà không làm ảnh hưởng đến cấu trúc hiện có và đảm bảo code dễ bảo trì, mở rộng trong tương lai.

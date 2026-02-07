<?php

namespace App\Filament\Pages;

use App\Models\LoginHistory;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MyProfile extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = '내 설정';
    protected static ?string $navigationLabel = '프로필 관리';
    protected static ?string $title = '프로필 관리';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.my-profile';

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->profileForm->fill([
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
        ]);

        $this->passwordForm->fill();
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->description('이름, 이메일, 프로필 사진을 수정합니다.')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('프로필 사진')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('name')
                            ->label('이름')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('이메일')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('비밀번호 변경')
                    ->description('비밀번호를 변경합니다. 변경하지 않으려면 비워두세요.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('현재 비밀번호')
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        Forms\Components\TextInput::make('password')
                            ->label('새 비밀번호')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->different('current_password'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('새 비밀번호 확인')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ])->columns(1),
            ])
            ->statePath('passwordData');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LoginHistory::query()
                    ->where('user_id', auth()->id())
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('event')
                    ->label('구분')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'login' => '로그인',
                        'logout' => '로그아웃',
                        'login_failed' => '로그인 실패',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'gray',
                        'login_failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP 주소'),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('브라우저')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->user_agent),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('일시')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50])
            ->emptyStateHeading('로그인 기록이 없습니다')
            ->emptyStateDescription('로그인/로그아웃 기록이 여기에 표시됩니다.')
            ->emptyStateIcon('heroicon-o-finger-print');
    }

    public function saveProfile(): void
    {
        $data = $this->profileForm->getState();

        $user = auth()->user();
        $user->update([
            'name' => $data['name'],
            'avatar_url' => $data['avatar_url'],
        ]);

        Notification::make()
            ->title('프로필이 업데이트되었습니다.')
            ->success()
            ->send();
    }

    public function savePassword(): void
    {
        $data = $this->passwordForm->getState();

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        $this->passwordForm->fill();

        Notification::make()
            ->title('비밀번호가 변경되었습니다.')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}

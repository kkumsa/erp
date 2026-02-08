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
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.my-profile';

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.my_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.my_profile');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('common.pages.my_profile');
    }

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
                Forms\Components\Section::make(__('common.sections.basic_info'))
                    ->description(__('common.helpers.profile_description'))
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label(__('fields.profile_photo'))
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
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('fields.email'))
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
                Forms\Components\Section::make(__('common.sections.password_change'))
                    ->description(__('common.helpers.password_description'))
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label(__('fields.current_password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        Forms\Components\TextInput::make('password')
                            ->label(__('fields.new_password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->different('current_password'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label(__('fields.password_confirmation'))
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
                    ->label(__('fields.event'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __('common.events.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'gray',
                        'login_failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('fields.ip_address')),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label(__('fields.user_agent'))
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->user_agent),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.datetime'))
                    ->dateTime('Y.m.d H:i:s')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50])
            ->emptyStateHeading(__('common.empty_states.no_login_history'))
            ->emptyStateDescription(__('common.empty_states.login_history_description'))
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
            ->title(__('common.notifications.profile_updated'))
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
            ->title(__('common.notifications.password_changed'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}

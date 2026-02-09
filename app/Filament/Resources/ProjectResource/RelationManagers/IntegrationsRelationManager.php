<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\ProjectIntegration;
use App\Services\JiraIntegrationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class IntegrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'integrations';

    protected static ?string $title = '이슈 연동 (JIRA 등)';

    protected static ?string $modelLabel = '연동';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider')
                    ->label('연동 대상')
                    ->options(['jira' => 'JIRA (Cloud / Server)'])
                    ->default('jira')
                    ->required(),

                Forms\Components\TextInput::make('config.base_url')
                    ->label('JIRA URL')
                    ->url()
                    ->placeholder('https://your-domain.atlassian.net')
                    ->required(),

                Forms\Components\TextInput::make('config.project_key')
                    ->label('프로젝트 키')
                    ->placeholder('PROJ')
                    ->required(),

                Forms\Components\TextInput::make('config.email')
                    ->label('이메일 (Atlassian 계정)')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('config.api_token')
                    ->label('API 토큰')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Atlassian 계정 설정에서 API 토큰 발급. 수정 시 비워두면 기존 값 유지'),

                Forms\Components\Select::make('sync_direction')
                    ->label('동기화 방향')
                    ->options([
                        ProjectIntegration::SYNC_JIRA_TO_ERP => 'JIRA → ERP (JIRA가 주)',
                        ProjectIntegration::SYNC_ERP_TO_JIRA => 'ERP → JIRA (ERP가 주)',
                        ProjectIntegration::SYNC_BIDIRECTIONAL => '양방향',
                    ])
                    ->default(ProjectIntegration::SYNC_JIRA_TO_ERP)
                    ->required(),

                Forms\Components\TextInput::make('config.issue_type_id')
                    ->label('이슈 타입 ID (ERP→JIRA 생성 시)')
                    ->default('10001')
                    ->helperText('JIRA 기본 Task 타입은 10001'),

                Forms\Components\Toggle::make('is_active')
                    ->label('활성화')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->label('연동')
                    ->badge()
                    ->formatStateUsing(fn ($s) => $s === 'jira' ? 'JIRA' : $s),

                Tables\Columns\TextColumn::make('sync_direction')
                    ->label('방향')
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'jira_to_erp' => 'JIRA → ERP',
                        'erp_to_jira' => 'ERP → JIRA',
                        'bidirectional' => '양방향',
                        default => $s,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성')
                    ->boolean(),

                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('마지막 동기화')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('last_sync_error')
                    ->label('마지막 오류')
                    ->limit(40)
                    ->placeholder('-'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('sync')
                    ->label('지금 동기화 (JIRA→ERP)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn (ProjectIntegration $record) => $record->provider === 'jira' && $record->canPullFromExternal())
                    ->action(function (ProjectIntegration $record) {
                        $service = app(JiraIntegrationService::class);
                        $result = $service->pullFromJira($record);
                        if (!empty($result['error'])) {
                            Notification::make()->title('동기화 실패')->body($result['error'])->danger()->send();
                            return;
                        }
                        Notification::make()
                            ->title('동기화 완료')
                            ->body("생성 {$result['created']}건, 갱신 {$result['updated']}건")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataBeforeSave(function (array $data, ProjectIntegration $record): array {
                        if (empty($data['config']['api_token']) && !empty($record->config['api_token'])) {
                            $data['config']['api_token'] = $record->config['api_token'];
                        }
                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

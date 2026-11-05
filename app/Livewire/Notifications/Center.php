<?php

namespace App\Livewire\Notifications;

use App\Models\User;
use App\Services\MenuAccessService;
use App\Services\ReminderService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Center extends Component
{
    /**
     * @var list<array{type: string, title: string, message: string, url: string, date: string, severity: string}>
     */
    #[Locked]
    public array $reminders = [];

    /**
     * @var list<array{key: string, label: string, route: string, message: string, auto_enable_at: ?string}>
     */
    #[Locked]
    public array $disabledMenus = [];

    #[Locked]
    public bool $showLoginPopup = false;

    #[Locked]
    public bool $showMenuAccessPopup = false;

    #[Locked]
    public int $popupSeconds = 15;

    #[Locked]
    public int $reminderCount = 0;

    public function mount(ReminderService $reminderService, MenuAccessService $menuAccess): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $this->reminders = $reminderService->forUser($user);
        $this->reminderCount = count($this->reminders);

        if ($this->reminders !== [] && ! session()->has('reminder_popup_shown')) {
            $this->showLoginPopup = true;
            session()->put('reminder_popup_shown', true);
        }

        $this->refreshMenuAccess($menuAccess);

        if ($this->showLoginPopup) {
            session()->put('reminder_popup_dismiss_after', now()->addSeconds($this->popupSeconds)->timestamp);
        }
    }

    #[On('menu-access-updated')]
    public function refreshMenuAccess(MenuAccessService $menuAccess): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $this->disabledMenus = $menuAccess->disabledMenusForUser($user);
        $this->showMenuAccessPopup = $this->disabledMenus !== [];
        if ($this->disabledMenus === [] || session()->has('menu_access_popup_shown')) {
            return;
        }

        $this->showLoginPopup = true;
        session()->put('menu_access_popup_shown', true);
        session()->put('reminder_popup_dismiss_after', now()->addSeconds($this->popupSeconds)->timestamp);
    }

    public function dismissLoginPopup(): void
    {
        if (
            ! $this->showLoginPopup
            || now()->timestamp < (int) session('reminder_popup_dismiss_after', PHP_INT_MAX)
        ) {
            return;
        }

        $this->showLoginPopup = false;
        $this->showMenuAccessPopup = false;
        session()->forget('reminder_popup_dismiss_after');
    }

    public function render(): View
    {
        return view('livewire.notifications.center');
    }
}

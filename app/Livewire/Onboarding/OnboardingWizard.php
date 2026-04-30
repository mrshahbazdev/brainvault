<?php

namespace App\Livewire\Onboarding;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OnboardingWizard extends Component
{
    public int $step = 1;
    public int $totalSteps = 4;

    public string $name = '';
    public string $timezone = 'UTC';
    public string $theme = 'system';
    public array $interests = [];
    public bool $installExtension = false;

    protected array $availableInterests = [
        'web-development' => 'Web Development',
        'ai-ml' => 'AI & Machine Learning',
        'design' => 'Design & UX',
        'devops' => 'DevOps & Cloud',
        'mobile' => 'Mobile Development',
        'data-science' => 'Data Science',
        'security' => 'Cybersecurity',
        'productivity' => 'Productivity',
        'business' => 'Business & Startup',
        'learning' => 'Learning & Courses',
    ];

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->timezone = $user->timezone ?? 'UTC';
        $this->theme = $user->theme ?? 'system';
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate(['name' => 'required|min:2']);
        }

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function toggleInterest(string $interest): void
    {
        if (in_array($interest, $this->interests)) {
            $this->interests = array_values(array_diff($this->interests, [$interest]));
        } else {
            $this->interests[] = $interest;
        }
    }

    public function complete(): void
    {
        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'timezone' => $this->timezone,
            'theme' => $this->theme,
            'settings' => array_merge($user->settings ?? [], [
                'interests' => $this->interests,
            ]),
            'onboarding_completed' => true,
        ]);

        $this->redirect(route('dashboard'));
    }

    public function skip(): void
    {
        Auth::user()->update(['onboarding_completed' => true]);
        $this->redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.onboarding.onboarding-wizard', [
            'availableInterests' => $this->availableInterests,
        ])->layout('layouts.app', ['title' => 'Welcome to BrainVault']);
    }
}

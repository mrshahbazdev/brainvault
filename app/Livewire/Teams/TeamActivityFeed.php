<?php

namespace App\Livewire\Teams;

use App\Models\TeamActivity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TeamActivityFeed extends Component
{
    use WithPagination;

    public int $teamId;

    public function render()
    {
        $team = Auth::user()->teams()->findOrFail($this->teamId);

        $activities = TeamActivity::where('team_id', $this->teamId)
            ->with(['user', 'subject'])
            ->latest('created_at')
            ->paginate(30);

        return view('livewire.teams.team-activity-feed', [
            'team' => $team,
            'activities' => $activities,
        ])->layout('layouts.app', ['title' => 'Team Activity']);
    }
}

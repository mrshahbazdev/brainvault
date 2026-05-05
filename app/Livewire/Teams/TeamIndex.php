<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class TeamIndex extends Component
{
    public bool $showCreateModal = false;
    public bool $showInviteModal = false;
    public string $newName = '';
    public string $newDescription = '';
    public string $inviteEmail = '';
    public string $inviteRole = 'member';
    public ?int $selectedTeamId = null;

    public function openCreateModal(): void
    {
        $this->showCreateModal = true;
        $this->newName = '';
        $this->newDescription = '';
    }

    public function createTeam(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newDescription' => 'nullable|string|max:1000',
        ]);

        $team = Team::create([
            'name' => $this->newName,
            'slug' => Str::slug($this->newName) . '-' . Str::random(4),
            'description' => $this->newDescription ?: null,
            'owner_id' => Auth::id(),
        ]);

        $team->members()->attach(Auth::id(), [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        $this->showCreateModal = false;
    }

    public function openInviteModal(int $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->showInviteModal = true;
        $this->inviteEmail = '';
        $this->inviteRole = 'member';
    }

    public function sendInvite(): void
    {
        $this->validate([
            'inviteEmail' => 'required|email',
            'inviteRole' => 'required|in:member,editor,admin',
        ]);

        $team = Team::where('id', $this->selectedTeamId)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        \DB::table('team_invitations')->insert([
            'team_id' => $team->id,
            'invited_by' => Auth::id(),
            'email' => $this->inviteEmail,
            'role' => $this->inviteRole,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->showInviteModal = false;
    }

    public function deleteTeam(int $id): void
    {
        Team::where('id', $id)->where('owner_id', Auth::id())->delete();
    }

    public function render()
    {
        $ownedTeams = Auth::user()->ownedTeams()->with('members')->get();
        $memberTeams = Auth::user()->teams ?? collect();

        return view('livewire.teams.team-index', [
            'ownedTeams' => $ownedTeams,
            'memberTeams' => $memberTeams,
        ])->layout('layouts.app', ['title' => 'Teams']);
    }
}

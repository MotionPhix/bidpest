<?php

namespace App\Policies;

use App\Models\TenderDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TenderDocumentPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return false;
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, TenderDocument $tenderDocument): bool
  {
    return $user->can('view tender') || $user->id === $tenderDocument->user_id;
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return false;
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, TenderDocument $tenderDocument): bool
  {
    return $user->can('edit tender') || $user->id === $tenderDocument->user_id;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, TenderDocument $tenderDocument): bool
  {
    return $user->can('delete tender') || $user->id === $tenderDocument->user_id;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, TenderDocument $tenderDocument): bool
  {
    return false;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, TenderDocument $tenderDocument): bool
  {
    return false;
  }
}

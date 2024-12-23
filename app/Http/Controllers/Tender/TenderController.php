<?php

namespace App\Http\Controllers\Tender;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTenderDocumentRequest;
use App\Jobs\AnalyzeTenderDocumentJob;
use App\Jobs\GenerateBidJob;
use App\Models\TenderDocument;
use App\Services\AIBidAnalysisService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TenderController extends Controller implements HasMiddleware
{
  use AuthorizesRequests;

  public function __construct(
    private AIBidAnalysisService $aiBidAnalysisService
  )
  {
  }

  public static function middleware()
  {
    return [
      new Middleware('permission:create tender', only: ['create', 'store']),
      new Middleware('permission:edit tender', only: ['edit', 'update']),
      new Middleware('permission:delete tender', only: ['destroy']),
    ];
  }

  public function index()
  {
    $tenderDocuments = TenderDocument::with(['user:id,name', 'bids'])
      ->latest()
      ->paginate(10)
      ->through(fn($document) => [
        'id' => $document->id,
        'uuid' => $document->uuid,
        'title' => $document->title,
        'client_name' => $document->client_name,
        'submission_deadline' => $document->submission_deadline,
        'status' => $document->status,
        'bids_count' => $document->bids_count,
        'user' => $document->user ? [
          'name' => $document->user->name
        ] : null
      ]);

    return Inertia::render('TenderDocuments/Index', [
      'tenderDocuments' => $tenderDocuments
    ]);
  }

  public function create()
  {
    return Inertia::render('TenderDocuments/Create');
  }

  public function store(CreateTenderDocumentRequest $request)
  {
    try {
      DB::beginTransaction();

      // Create tender document
      $tenderDocument = TenderDocument::create([
        'title' => $request->title,
        'description' => $request->description,
        'client_name' => $request->client_name,
        'submission_deadline' => $request->submission_deadline,
        'requirements' => $request->requirements ?? [],
        'user_id' => auth()->id(),
        'status' => 'draft'
      ]);

      // Dispatch AI analysis job
      AnalyzeTenderDocumentJob::dispatch($tenderDocument)
        ->delay(now()->addSeconds(10)); // Optional delay

      DB::commit();

      // Log the action
      Log::info('Tender Document Created', [
        'user_id' => auth()->id(),
        'tender_document_id' => $tenderDocument->id
      ]);

      // Redirect with success message
      return redirect()
        ->route('tender-documents.show', $tenderDocument)
        ->with('success', 'Tender document created successfully. AI analysis is in progress.');
    } catch (\Exception $e) {
      DB::rollBack();

      // Log the error
      Log::error('Tender Document Creation Failed', [
        'error' => $e->getMessage(),
        'user_id' => auth()->id()
      ]);

      // Return with error message
      return back()
        ->withInput()
        ->with('error', 'Failed to create tender document. Please try again.');
    }
  }

  /*public function store(CreateTenderDocumentRequest $request)
  {
    $tenderDocument = TenderDocument::create($request->validated());

    // Dispatch the job to analyze the tender document
    AnalyzeTenderDocumentJob::dispatch($tenderDocument);

    return redirect()->route('bid-documents.index')
      ->with('success', 'Tender document created and analysis is in progress.');
  }*/

  public function show(TenderDocument $tenderDocument)
  {
    // Eager load related data
    $tenderDocument->load(['user:id,name', 'bids']);

    return Inertia::render('TenderDocuments/Show', [
      'tenderDocument' => [
        'id' => $tenderDocument->id,
        'uuid' => $tenderDocument->uuid,
        'title' => $tenderDocument->title,
        'description' => $tenderDocument->description,
        'client_name' => $tenderDocument->client_name,
        'submission_deadline' => $tenderDocument->submission_deadline,
        'status' => $tenderDocument->status,
        'requirements' => $tenderDocument->requirements,
        'user' => [
          'name' => $tenderDocument->user->name
        ],
        'bids' => $tenderDocument->bids->map(fn($bid) => [
          'id' => $bid->id,
          'status' => $bid->status,
          // Add other relevant bid information
        ])
      ]
    ]);
  }

  public function edit(TenderDocument $tenderDocument)
  {
    // Ensure user can only edit their own documents
    $this->authorize('update', $tenderDocument);

    return Inertia::render('TenderDocuments/Edit', [
      'tenderDocument' => $tenderDocument
    ]);
  }

  public function update(UpdateTenderDocumentRequest $request, TenderDocument $tenderDocument)
  {
    // Ensure user can only update their own documents
    $this->authorize('update', $tenderDocument);

    try {
      DB::beginTransaction();

      $tenderDocument->update([
        'title' => $request->title,
        'description' => $request->description,
        'client_name' => $request->client_name,
        'submission_deadline' => $request->submission_deadline,
        'requirements' => $request->requirements ?? [],
        'status' => $request->status ?? $tenderDocument->status
      ]);

      // Optionally re-analyze if significant changes
      if ($request->has('reanalyze') && $request->reanalyze) {
        AnalyzeTenderDocumentJob::dispatch($tenderDocument);
      }

      DB::commit();

      Log::info('Tender Document Updated', [
        'user_id' => auth()->id(),
        'tender_document_id' => $tenderDocument->id
      ]);

      return redirect()
        ->route('tender-documents.show', $tenderDocument)
        ->with('success', 'Tender document updated successfully.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Tender Document Update Failed', [
        'error' => $e->getMessage(),
        'user_id' => auth()->id()
      ]);

      return back()
        ->withInput()
        ->with('error', 'Failed to update tender document. Please try again.');
    }
  }

  public function destroy(TenderDocument $tenderDocument)
  {
    // Ensure user can only delete their own documents
    $this->authorize('delete', $tenderDocument);

    try {
      DB::beginTransaction();

      // Delete associated bids
      $tenderDocument->bids()->delete();

      // Delete the tender document
      $tenderDocument->delete();

      DB::commit();

      Log::info('Tender Document Deleted', [
        'user_id' => auth()->id(),
        'tender_document_id' => $tenderDocument->id
      ]);

      return redirect()
        ->route('tender-documents.index')
        ->with('success', 'Tender document deleted successfully.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Tender Document Deletion Failed', [
        'error' => $e->getMessage(),
        'user_id' => auth()->id()
      ]);

      return back()
        ->with('error', 'Failed to delete tender document. Please try again.');
    }
  }

  public function generateBid(TenderDocument $tenderDocument)
  {
    $this->authorize('generate bid', $tenderDocument); // Ensure user has permission

    // Dispatch the job to generate the bid
    GenerateBidJob::dispatch($tenderDocument, auth()->user());

    return redirect()->back()->with('status', 'Bid generation in progress. You will be notified once it is complete.');
  }

  public function regenerateAnalysis(TenderDocument $tenderDocument)
  {
    // Ensure user can regenerate analysis
    $this->authorize('update', $tenderDocument);

    try {
      // Dispatch job to re-analyze the tender document
      AnalyzeTenderDocumentJob::dispatch($tenderDocument);

      return back()
        ->with('success ', 'AI analysis has been re-initiated successfully.');
    } catch (\Exception $e) {
      Log::error('Tender Document Analysis Regeneration Failed', [
        'error' => $e->getMessage(),
        'user_id' => auth()->id()
      ]);

      return back()
        ->with('error', 'Failed to regenerate AI analysis. Please try again.');
    }
  }
}

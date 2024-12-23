<?php

namespace App\Http\Controllers\Bid;

use App\Http\Controllers\Controller;
use App\Models\TenderDocument;
use App\Services\BidGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BidDocumentController extends Controller
{
  public function __construct(
    protected BidGenerationService $bidGenerationService
  ) {}

  public function index()
  {
    $tenderDocuments = TenderDocument::with('bids')->get();
    return Inertia::render('BidDocuments/Index', [
      'tenderDocuments' => $tenderDocuments
    ]);
  }

  public function create()
  {
    return Inertia::render('BidDocuments/Create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'client_name' => 'required|string|max:255',
      'submission_deadline' => 'required|date',
      'requirements' => 'nullable|array',
    ]);

    $tenderDocument = TenderDocument::create([
      'title' => $request->title,
      'description' => $request->description,
      'client_name' => $request->client_name,
      'submission_deadline' => $request->submission_deadline,
      'requirements' => $request->requirements,
      'user_id' => auth()->id(),
    ]);

    return redirect()->route('bid-documents.index')->with('status', 'Tender Document created successfully.');
  }

  public function generateBid(Request $request, TenderDocument $tenderDocument)
  {
    $bid = $this->bidGenerationService->generateBidForTender($tenderDocument, auth()->user());

    return redirect()->route('bids.show', $bid->uuid)->with('status', 'Bid generated successfully.');
  }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Expense::class);

        $expenses = $request->user()
            ->expenses()
            ->with('category')
            ->latest('spent_at')
            ->latest('id')
            ->get();

        return ExpenseResource::collection($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $request->user()
            ->expenses()
            ->create($request->validated());

        $expense->load('category');

        return ExpenseResource::make($expense)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Expense $expense): ExpenseResource
    {
        $this->ensureOwnsExpense($request, $expense);

        $expense->load('category');

        return ExpenseResource::make($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): ExpenseResource
    {
        $expense->update($request->validated());
        $expense->load('category');

        return ExpenseResource::make($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Expense $expense): JsonResponse
    {
        $this->ensureOwnsExpense($request, $expense);

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully.',
        ]);
    }

    private function ensureOwnsExpense(Request $request, Expense $expense): void
    {
        if ($request->user()->cannot('view', $expense)) {
            abort(404);
        }
    }
}

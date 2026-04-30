@extends('layouts/layoutMaster')

@section('title', 'Create Invoice for Intern')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create Invoice for Intern</h4>
                <p class="text-muted mb-0">
                    Creating invoice for: <strong>{{ $internName }}</strong> ({{ $internEmail }})
                </p>
            </div>
            <div class="card-body">
<form action="{{ route('admin.invoices.store') }}" method="POST">
                        @csrf
                    
                    <input type="hidden" name="name" value="{{ $internName }}">
                    <input type="hidden" name="intern_email" value="{{ $internEmail }}">
                    <input type="hidden" name="inv_id" value="{{ $newInvId }}">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact" class="form-control" value="{{ $internPhone ?? '' }}" placeholder="Intern contact number">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Technology</label>
                            <input type="text" name="technology" class="form-control" value="{{ $internTechnology ?? '' }}" readonly style="background-color: #f5f5f5;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Type</label>
                            <select name="invoice_type" class="form-select" required>
                                <option value="Internship" selected>Internship Fee</option>
                                <option value="Course">Course Fee</option>
                                <option value="Project">Project Fee</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Amount (PKR)</label>
                            <input type="number" name="total_amount" class="form-control" step="0.01" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Received Amount (PKR)</label>
                            <input type="number" name="received_amount" class="form-control" step="0.01" value="0">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                            <small class="text-muted">Required if remaining amount > 0</small>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Create Invoice</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
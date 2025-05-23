@extends('admin.layout')

@section('title', 'Settings')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">System Settings</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-cogs me-1"></i>
                Application Settings
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-3">General Settings</h5>
                            
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings['site_name']['value'] ?? 'Laravel App' }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Site Description</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description']['value'] ?? 'Your Online Learning Platform' }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ $settings['contact_email']['value'] ?? 'contact@example.com' }}" required>
                            </div>
                            
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode']['value'] ?? 0) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                                <div class="form-text">When enabled, only administrators can access the site.</div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <h5 class="mb-3">Payment & Commission Settings</h5>
                            
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" {{ ($settings['currency']['value'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ ($settings['currency']['value'] ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ ($settings['currency']['value'] ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="instructor_commission_rate" class="form-label">Instructor Commission Rate (%)</label>
                                <input type="number" class="form-control" id="instructor_commission_rate" name="instructor_commission_rate" value="{{ $settings['instructor_commission_rate']['value'] ?? 70 }}" min="0" max="100" required>
                                <div class="form-text">Percentage of course sales that goes to instructors.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="minimum_withdrawal" class="form-label">Minimum Withdrawal Amount</label>
                                <input type="number" class="form-control" id="minimum_withdrawal" name="minimum_withdrawal" value="{{ $settings['minimum_withdrawal']['value'] ?? 50 }}" min="0" required>
                                <div class="form-text">Minimum amount instructors must earn before they can withdraw.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_methods" class="form-label">Enabled Payment Methods</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payment_method_credit_card" name="payment_methods[]" value="credit_card" {{ in_array('credit_card', explode(',', $settings['payment_methods']['value'] ?? 'credit_card')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_credit_card">Credit Card</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payment_method_paypal" name="payment_methods[]" value="paypal" {{ in_array('paypal', explode(',', $settings['payment_methods']['value'] ?? 'credit_card')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_paypal">PayPal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payment_method_bank_transfer" name="payment_methods[]" value="bank_transfer" {{ in_array('bank_transfer', explode(',', $settings['payment_methods']['value'] ?? 'credit_card')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_bank_transfer">Bank Transfer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payment_method_paymob" name="payment_methods[]" value="paymob" {{ in_array('paymob', explode(',', $settings['payment_methods']['value'] ?? '')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_paymob">Paymob</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payment_method_vodafone_cash" name="payment_methods[]" value="vodafone_cash" {{ in_array('vodafone_cash', explode(',', $settings['payment_methods']['value'] ?? '')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_vodafone_cash">Vodafone Cash</label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="default_payment_method" class="form-label">Default Payment Method</label>
                                <select class="form-select" id="default_payment_method" name="default_payment_method">
                                    <option value="credit_card" {{ ($settings['default_payment_method']['value'] ?? 'credit_card') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="paypal" {{ ($settings['default_payment_method']['value'] ?? '') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="bank_transfer" {{ ($settings['default_payment_method']['value'] ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="paymob" {{ ($settings['default_payment_method']['value'] ?? '') == 'paymob' ? 'selected' : '' }}>Paymob</option>
                                    <option value="vodafone_cash" {{ ($settings['default_payment_method']['value'] ?? '') == 'vodafone_cash' ? 'selected' : '' }}>Vodafone Cash</option>
                                </select>
                                <div class="form-text">This payment method will be selected by default on the checkout page.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Email Settings</h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="mail_driver" class="form-label">Mail Driver</label>
                                    <select class="form-select" id="mail_driver" name="mail_driver">
                                        <option value="smtp" {{ ($settings['mail_driver']['value'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ ($settings['mail_driver']['value'] ?? 'smtp') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="mailgun" {{ ($settings['mail_driver']['value'] ?? 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_host" class="form-label">Mail Host</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ $settings['mail_host']['value'] ?? 'smtp.mailtrap.io' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_port" class="form-label">Mail Port</label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ $settings['mail_port']['value'] ?? 2525 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_username" class="form-label">Mail Username</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ $settings['mail_username']['value'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_password" class="form-label">Mail Password</label>
                                    <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ $settings['mail_password']['value'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="mail_encryption" class="form-label">Mail Encryption</label>
                                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                                        <option value="tls" {{ ($settings['mail_encryption']['value'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($settings['mail_encryption']['value'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ ($settings['mail_encryption']['value'] ?? 'tls') == '' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-wrench me-1"></i>
                System Maintenance
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Clear Cache</h5>
                                <p class="card-text">Clear application cache, route cache, and configuration cache.</p>
                                <button type="button" class="btn btn-primary" id="clearCacheBtn">
                                    <i class="fas fa-broom me-1"></i> Clear Cache
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Database Backup</h5>
                                <p class="card-text">Generate a backup of your database.</p>
                                <button type="button" class="btn btn-primary" id="backupDatabaseBtn">
                                    <i class="fas fa-database me-1"></i> Backup Database
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Settings History
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Setting Key</th>
                                <th>Updated Value</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $key => $setting)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>
                                        @if(is_array($setting['value']))
                                            {{ implode(', ', $setting['value']) }}
                                        @else
                                            {{ $setting['value'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($setting['updated_by'])
                                            {{ App\Models\User::find($setting['updated_by'])->name ?? 'Unknown' }}
                                        @else
                                            System
                                        @endif
                                    </td>
                                    <td>{{ $setting['updated_at']->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clear cache button action
        document.getElementById('clearCacheBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all caches?')) {
                // Here you would typically make an AJAX request to a clear-cache endpoint
                alert('Cache cleared successfully!');
            }
        });
        
        // Backup database button action
        document.getElementById('backupDatabaseBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to backup the database?')) {
                // Here you would typically make an AJAX request to a backup-database endpoint
                alert('Database backup started. You will be notified when it completes.');
            }
        });
    });
</script>
@endsection 
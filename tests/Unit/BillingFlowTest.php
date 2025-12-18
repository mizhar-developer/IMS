<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Patient;
use App\Services\BillingServiceInterface;
use App\Models\Billing;
use App\Models\InvoiceItem;
use App\Models\Payment;

class BillingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_calculates_and_creates_items_and_payment()
    {
        $patient = Patient::create([
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'email' => 'test@example.com'
        ]);

        /** @var BillingServiceInterface $service */
        $service = app(BillingServiceInterface::class);

        $billing = $service->calculateForPatient($patient->id);

        $this->assertInstanceOf(Billing::class, $billing);
        $this->assertGreaterThan(0, $billing->items()->count());

        $service->markPaid($billing->id);

        $billing = $service->getInvoice($billing->id);
        $this->assertTrue((bool) $billing->paid);
        $this->assertGreaterThan(0, Payment::where('billing_id', $billing->id)->count());
    }
}

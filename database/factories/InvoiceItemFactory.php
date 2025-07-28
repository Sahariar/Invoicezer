<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 10);
        $unitPrice = $this->faker->randomFloat(2, 25, 500);
        $total = round($quantity * $unitPrice, 2);

        $service = $this->faker->randomElement([
            'Web Development Services',
            'Mobile App Development',
            'UI/UX Design Services',
            'Digital Marketing Campaign',
            'SEO Optimization',
            'Content Writing Services',
            'Database Management',
            'Server Maintenance',
            'Software Consulting',
            'Project Management',
            'Quality Assurance Testing',
            'System Integration',
            'API Development',
            'Logo Design',
            'Brand Identity Package',
            'Social Media Management',
            'Email Marketing Setup',
            'Website Hosting',
            'Domain Registration',
            'SSL Certificate Setup',
            'Backup Solutions',
            'Security Audit',
            'Performance Optimization',
            'Training Sessions',
            'Technical Support',
            'Code Review',
            'Documentation Services',
            'Deployment Services',
            'Maintenance & Updates',
            'Custom Plugin Development',
        ]);

        return [
            'invoice_id' => Invoice::factory(),
            'name' => $service, // Add name field
            'description' => $service,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $total,
        ];
    }
     public function forInvoice(Invoice $invoice): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_id' => $invoice->id,
        ]);
    }

    public function hourlyService(): static
    {
        $hours = $this->faker->randomFloat(2, 1, 40);
        $hourlyRate = $this->faker->randomElement([50, 75, 100, 125, 150, 175, 200]);
        $serviceName = $this->faker->randomElement([
            'Web Development (Hours)',
            'Consulting (Hours)',
            'Design Work (Hours)',
            'Project Management (Hours)',
            'Technical Support (Hours)',
        ]);

        $serviceDescription = $this->faker->randomElement([
                'Web development services for July 2025, including bug fixes and new feature implementation.',
                'Monthly SEO services including keyword tracking, audits, and on-page optimization tasks.',
                'Hosting and maintenance for client website – uptime monitoring, backups, and security updates.',
                'UI/UX design revisions and prototype updates as discussed in July project scope.',
                'Payment for 10 hours of consulting work related to Laravel backend optimization and deployment.',
                'Plugin customization and integration with WooCommerce checkout flow for better user experience.',
                'Domain renewal, DNS configuration, and SSL setup for the clients ecommerce site.',
        ]);

        return $this->state(fn (array $attributes) => [
            'name' => $serviceName,
            'description' => $serviceDescription,
            'quantity' => $hours,
            'unit_price' => $hourlyRate,
            'total' => round($hours * $hourlyRate, 2),
        ]);
    }

    public function fixedPrice(): static
    {
        $price = $this->faker->randomElement([500, 1000, 1500, 2000, 2500, 3000, 5000]);
        $projectName = $this->faker->randomElement([
            'Website Redesign Project',
            'Mobile App Development',
            'E-commerce Setup',
            'Custom CRM Development',
            'Brand Identity Package',
        ]);

        $projectDescription = $this->faker->randomElement([
                'Web development services for July 2025, including bug fixes and new feature implementation.',
                'Monthly SEO services including keyword tracking, audits, and on-page optimization tasks.',
                'Hosting and maintenance for client website – uptime monitoring, backups, and security updates.',
                'UI/UX design revisions and prototype updates as discussed in July project scope.',
                'Payment for 10 hours of consulting work related to Laravel backend optimization and deployment.',
                'Plugin customization and integration with WooCommerce checkout flow for better user experience.',
                'Domain renewal, DNS configuration, and SSL setup for the clients ecommerce site.',
        ]);

        return $this->state(fn (array $attributes) => [
            'name' => $projectName,
            'description' => $projectDescription,
            'quantity' => 1,
            'unit_price' => $price,
            'total' => $price,
        ]);
    }
}

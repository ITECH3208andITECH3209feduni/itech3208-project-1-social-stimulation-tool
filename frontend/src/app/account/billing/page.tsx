import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Billing - Scenariaid',
    description: 'Manage your Scenariaid subscription, billing information, and payment methods.',
};

export default function BillingPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Billing" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Header */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-red">Billing & Subscription</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Manage your subscription plans, billing history, and payment methods 
                                all in one secure location.
                            </P>
                        </div>

                        {/* Current Plan */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle>Current Plan</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex items-center justify-between mb-6">
                                    <div>
                                        <P className="text-sm text-muted-foreground">Current Plan</P>
                                        <P className="text-2xl font-bold text-primary-blue">Professional</P>
                                    </div>
                                    <button className="bg-primary-red text-white px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-red-700">
                                        Upgrade Plan
                                    </button>
                                </div>
                                <div className="space-y-2">
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Monthly Price</span>
                                        <span className="text-2xl font-bold">$29.99</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Next Billing Date</span>
                                        <span className="text-lg font-semibold">May 25, 2026</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Auto-renewal</span>
                                        <span className="text-lg font-semibold text-green-600">Enabled</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Billing History */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle>Billing History</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div className="border-l-4 border-primary-red pl-4">
                                        <div className="flex justify-between items-center">
                                            <div>
                                                <P className="font-semibold">April 25, 2026</P>
                                                <P className="text-sm text-muted-foreground">Professional Plan - Monthly</P>
                                            </div>
                                            <div className="text-right">
                                                <span className="text-lg font-bold text-primary-red">-$29.99</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="border-l-4 border-primary-blue pl-4">
                                        <div className="flex justify-between items-center">
                                            <div>
                                                <P className="font-semibold">March 25, 2026</P>
                                                <P className="text-sm text-muted-foreground">Professional Plan - Monthly</P>
                                            </div>
                                            <div className="text-right">
                                                <span className="text-lg font-bold text-primary-red">-$29.99</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="border-l-4 border-primary-light pl-4">
                                        <div className="flex justify-between items-center">
                                            <div>
                                                <P className="font-semibold">February 25, 2026</P>
                                                <P className="text-sm text-muted-foreground">Professional Plan - Monthly</P>
                                            </div>
                                            <div className="text-right">
                                                <span className="text-lg font-bold text-primary-red">-$29.99</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Payment Methods */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle>Payment Methods</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div className="border rounded-lg p-4">
                                        <div className="flex items-center space-x-4 mb-2">
                                            <div className="w-12 h-8 bg-primary-blue rounded-full flex items-center justify-center">
                                                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H3z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m7 8h3m-3 4h3m-6 4h3" />
                                                </svg>
                                            </div>
                                            <div>
                                                <P className="font-semibold">Visa ending in 4242</P>
                                                <P className="text-sm text-muted-foreground">Default</P>
                                            </div>
                                        </div>
                                        <button className="w-full text-left text-primary-blue hover:underline">
                                            Manage Payment Methods
                                        </button>
                                    </div>
                                    
                                    <div className="border rounded-lg p-4">
                                        <div className="flex items-center space-x-4 mb-2">
                                            <div className="w-12 h-8 bg-primary-red rounded-full flex items-center justify-center">
                                                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H3z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m7 8h3m-3 4h3m-6 4h3" />
                                                </svg>
                                            </div>
                                            <div>
                                                <P className="font-semibold">Mastercard ending in 5555</P>
                                                <P className="text-sm text-muted-foreground">Backup</P>
                                            </div>
                                        </div>
                                        <button className="w-full text-left text-primary-blue hover:underline">
                                            Manage Payment Methods
                                        </button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Invoices */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle>Recent Invoices</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div className="flex justify-between items-center p-4 border rounded-lg">
                                        <div>
                                            <P className="font-semibold">Invoice #2024-04-001</P>
                                            <P className="text-sm text-muted-foreground">April 25, 2026</P>
                                        </div>
                                        <div className="text-right">
                                            <span className="text-lg font-bold text-primary-red">$29.99</span>
                                            <button className="ml-4 text-primary-blue hover:underline">
                                                Download PDF
                                            </button>
                                        </div>
                                    </div>
                                    <div className="flex justify-between items-center p-4 border rounded-lg">
                                        <div>
                                            <P className="font-semibold">Invoice #2024-03-001</P>
                                            <P className="text-sm text-muted-foreground">March 25, 2026</P>
                                        </div>
                                        <div className="text-right">
                                            <span className="text-lg font-bold text-green-600">Paid</span>
                                            <button className="ml-4 text-primary-blue hover:underline">
                                                Download PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </Container>
            </main>
        </div>
    );
}

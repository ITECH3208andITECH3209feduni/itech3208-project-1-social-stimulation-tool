import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Terms and Conditions - Scenariaid',
    description: 'Read our terms and conditions for using Scenariaid platform and services.',
};

export default function TermsPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Terms and Conditions" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Header */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-red">Terms and Conditions</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Please read these terms carefully before using our services. 
                                By using Scenariaid, you agree to be bound by these conditions.
                            </P>
                        </div>

                        {/* Last Updated */}
                        <Card className="mb-8">
                            <CardHeader>
                                <CardTitle className="text-primary-blue">Last Updated</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <P>
                                    <strong>Effective Date:</strong> April 25, 2026<br/>
                                    <strong>Version:</strong> 1.0.0
                                </P>
                            </CardContent>
                        </Card>

                        {/* Table of Contents */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle>Table of Contents</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <ol className="space-y-2 list-decimal list-inside">
                                    <li><a href="#acceptance" className="text-primary-blue hover:underline">1. Acceptance of Terms</a></li>
                                    <li><a href="#services" className="text-primary-blue hover:underline">2. Services Description</a></li>
                                    <li><a href="#user-responsibilities" className="text-primary-blue hover:underline">3. User Responsibilities</a></li>
                                    <li><a href="#privacy" className="text-primary-blue hover:underline">4. Privacy Policy</a></li>
                                    <li><a href="#intellectual-property" className="text-primary-blue hover:underline">5. Intellectual Property</a></li>
                                    <li><a href="#limitation" className="text-primary-blue hover:underline">6. Limitation of Liability</a></li>
                                    <li><a href="#termination" className="text-primary-blue hover:underline">7. Termination</a></li>
                                </ol>
                            </CardContent>
                        </Card>

                        {/* Terms Sections */}
                        <div className="space-y-12">
                            {/* Acceptance */}
                            <Card id="acceptance">
                                <CardHeader>
                                    <CardTitle>1. Acceptance of Terms</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P className="mb-4">
                                        By accessing or using Scenariaid services, you acknowledge that you have 
                                        read, understood, and agree to be bound by these Terms and Conditions.
                                    </P>
                                    <P>
                                        If you do not agree to these terms, you should not use our services 
                                        or access our platform.
                                    </P>
                                </CardContent>
                            </Card>

                            {/* Services Description */}
                            <Card id="services">
                                <CardHeader>
                                    <CardTitle>2. Services Description</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P className="mb-4">
                                        Scenariaid provides a comprehensive frontend development platform including:
                                    </P>
                                    <ul className="space-y-2 list-disc list-inside ml-6">
                                        <li>Component library with reusable UI elements</li>
                                        <li>Design system with custom theming</li>
                                        <li>Development tools and utilities</li>
                                        <li>Documentation and tutorials</li>
                                        <li>Community support and resources</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            {/* User Responsibilities */}
                            <Card id="user-responsibilities">
                                <CardHeader>
                                    <CardTitle>3. User Responsibilities</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <P>
                                            As a user of Scenariaid, you agree to:
                                        </P>
                                        <ul className="space-y-2 list-disc list-inside ml-6">
                                            <li>Use the services in accordance with applicable laws</li>
                                            <li>Provide accurate and complete information</li>
                                            <li>Maintain the security of your account credentials</li>
                                            <li>Report any issues or violations promptly</li>
                                            <li>Respect intellectual property rights</li>
                                        </ul>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Privacy Policy */}
                            <Card id="privacy">
                                <CardHeader>
                                    <CardTitle>4. Privacy Policy</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P className="mb-4">
                                        We are committed to protecting your privacy and personal information.
                                    </P>
                                    <ul className="space-y-2 list-disc list-inside ml-6">
                                        <li>Collect only necessary information for service provision</li>
                                        <li>Use information solely for improving our services</li>
                                        <li>Implement appropriate security measures</li>
                                        <li>Not share personal information without consent</li>
                                        <li>Provide transparency about data usage practices</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            {/* Intellectual Property */}
                            <Card id="intellectual-property">
                                <CardHeader>
                                    <CardTitle>5. Intellectual Property</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P className="mb-4">
                                        All content, features, and functionality provided by Scenariaid 
                                        are protected by intellectual property laws.
                                    </P>
                                    <ul className="space-y-2 list-disc list-inside ml-6">
                                        <li>You may not copy, modify, or distribute our proprietary code</li>
                                        <li>Custom designs and implementations remain your property</li>
                                        <li>We retain ownership of all platform improvements</li>
                                        <li>Open-source components are subject to their respective licenses</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            {/* Limitation of Liability */}
                            <Card id="limitation">
                                <CardHeader>
                                    <CardTitle>6. Limitation of Liability</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P className="mb-4">
                                        To the fullest extent permitted by law, Scenariaid shall not be liable 
                                        for any indirect, incidental, or consequential damages.
                                    </P>
                                    <ul className="space-y-2 list-disc list-inside ml-6">
                                        <li>Services provided "as is" without warranties of any kind</li>
                                        <li>We are not responsible for third-party integrations</li>
                                        <li>Limitation of damages to the amount paid for services</li>
                                        <li>User assumes all risk for custom implementations</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            {/* Termination */}
                            <Card id="termination">
                                <CardHeader>
                                    <CardTitle>7. Termination</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P className="mb-4">
                                        These terms remain in effect until terminated by either party.
                                    </P>
                                    <ul className="space-y-2 list-disc list-inside ml-6">
                                        <li>You may terminate your account at any time</li>
                                        <li>We may suspend accounts for violations</li>
                                        <li>Termination does not affect accrued obligations</li>
                                        <li>Data retention policies apply post-termination</li>
                                    </ul>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Contact */}
                        <Card className="bg-primary-light">
                            <CardHeader>
                                <CardTitle>Questions?</CardTitle>
                            </CardHeader>
                            <CardContent className="text-center">
                                <P className="mb-4">
                                    If you have any questions about these Terms and Conditions, 
                                    please don't hesitate to contact us.
                                </P>
                                <div className="flex gap-4 justify-center">
                                    <a 
                                        href="/contact" 
                                        className="inline-flex items-center justify-center rounded-md bg-primary-red px-6 py-3 text-white font-medium transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        Contact Support
                                    </a>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </Container>
            </main>
        </div>
    );
}

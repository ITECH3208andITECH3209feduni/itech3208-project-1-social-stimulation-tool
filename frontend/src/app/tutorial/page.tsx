import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, H3, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Tutorial - Scenariaid',
    description: 'Step-by-step tutorials to help you get started with Scenariaid platform.',
};

export default function TutorialPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Tutorial" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Hero Section */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-blue">Tutorial Center</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Learn how to use Scenariaid with our comprehensive tutorials. 
                                From basic setup to advanced features, we've got you covered.
                            </P>
                        </div>

                        {/* Getting Started */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle>Getting Started</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <P className="mb-4">
                                    Welcome to Scenariaid! This tutorial will guide you through 
                                    the essential features and help you get up and running quickly.
                                </P>
                                <div className="space-y-4">
                                    <div className="border-l-4 border-primary-red pl-4">
                                        <H3 className="text-lg font-semibold mb-2">1. Installation</H3>
                                        <P>
                                            Follow our installation guide to set up Scenariaid 
                                            on your local machine. All dependencies and requirements 
                                            are clearly documented.
                                        </P>
                                    </div>
                                    <div className="border-l-4 border-primary-blue pl-4">
                                        <H3 className="text-lg font-semibold mb-2">2. Basic Setup</H3>
                                        <P>
                                            Configure your environment and understand the basic 
                                            project structure. Learn about our component library 
                                            and design system.
                                        </P>
                                    </div>
                                    <div className="border-l-4 border-primary-light pl-4">
                                        <H3 className="text-lg font-semibold mb-2">3. First Component</H3>
                                        <P>
                                            Create your first component using our UI library. 
                                            Understand props, variants, and best practices.
                                        </P>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Advanced Topics */}
                        <div className="grid md:grid-cols-2 gap-8 mb-12">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Components</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P>
                                        Deep dive into our component library. Learn about 
                                        buttons, cards, forms, and layout components.
                                    </P>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Styling</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P>
                                        Master our design system with Sora font and custom colors. 
                                        Learn about theming and responsive design.
                                    </P>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Best Practices */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle className="text-primary-red">Best Practices</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <P>
                                        <strong>Code Organization:</strong> Follow our established 
                                        patterns and naming conventions for maintainable code.
                                    </P>
                                    <P>
                                        <strong>Performance:</strong> Learn optimization techniques 
                                        and best practices for fast, efficient applications.
                                    </P>
                                    <P>
                                        <strong>Accessibility:</strong> Build inclusive applications 
                                        that work for everyone with proper ARIA implementation.
                                    </P>
                                    <P>
                                        <strong>Testing:</strong> Write effective tests and ensure 
                                        your components work as expected.
                                    </P>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Resources */}
                        <Card className="bg-primary-light">
                            <CardHeader>
                                <CardTitle>Additional Resources</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <H3 className="font-semibold mb-2">Documentation</H3>
                                        <P>
                                            Check our comprehensive documentation for detailed 
                                            guides and API references.
                                        </P>
                                    </div>
                                    <div>
                                        <H3 className="font-semibold mb-2">Support</H3>
                                        <P>
                                            Get help from our team through various support channels 
                                            and community forums.
                                        </P>
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

import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, H3, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'About Us - Scenariaid',
    description: 'Learn more about Scenariaid - our mission, team, and commitment to excellence.',
};

export default function AboutPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="About Us" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Hero Section */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-red">About Scenariaid</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Building innovative solutions that transform ideas into reality. 
                                We are a team of passionate developers, designers, and innovators 
                                dedicated to creating exceptional digital experiences.
                            </P>
                        </div>

                        {/* Mission Section */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle className="text-primary-blue">Our Mission</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <P>
                                    To empower businesses and individuals with cutting-edge technology solutions 
                                    that drive growth, efficiency, and innovation. We believe in the power of 
                                    technology to transform challenges into opportunities and ideas into impactful realities.
                                </P>
                            </CardContent>
                        </Card>

                        {/* Values Section */}
                        <div className="grid md:grid-cols-3 gap-8 mb-12">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Innovation</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P>
                                        We constantly explore new technologies and approaches 
                                        to deliver solutions that push boundaries and exceed expectations.
                                    </P>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Quality</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P>
                                        Every line of code, every design element, and every user interaction 
                                        is crafted with meticulous attention to detail and excellence.
                                    </P>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Collaboration</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <P>
                                        We believe in the power of teamwork and partnerships, 
                                        working closely with clients to achieve shared success.
                                    </P>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Team Section */}
                        <Card className="mb-12">
                            <CardHeader>
                                <CardTitle className="text-primary-blue">Our Team</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <P className="mb-4">
                                    Our diverse team brings together expertise from various domains:
                                </P>
                                <div className="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <H3 className="text-lg font-semibold mb-2">Development</H3>
                                        <P>
                                            Full-stack developers skilled in modern frameworks 
                                            and best practices for scalable applications.
                                        </P>
                                    </div>
                                    <div>
                                        <H3 className="text-lg font-semibold mb-2">Design</H3>
                                        <P>
                                            Creative designers focused on user experience 
                                            and beautiful, intuitive interfaces.
                                        </P>
                                    </div>
                                    <div>
                                        <H3 className="text-lg font-semibold mb-2">Strategy</H3>
                                        <P>
                                            Strategic thinkers who ensure technology 
                                            aligns with business goals and user needs.
                                        </P>
                                    </div>
                                    <div>
                                        <H3 className="text-lg font-semibold mb-2">Support</H3>
                                        <P>
                                            Dedicated support team ensuring smooth 
                                            implementation and ongoing success.
                                        </P>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* CTA Section */}
                        <div className="text-center">
                            <Card className="bg-primary-light">
                                <CardContent className="py-8">
                                    <H2 className="mb-4">Ready to Work Together?</H2>
                                    <P className="mb-6 text-lg">
                                        Let's discuss how we can help bring your ideas to life 
                                        and create something extraordinary together.
                                    </P>
                                    <div className="flex gap-4 justify-center">
                                        <a 
                                            href="/contact" 
                                            className="inline-flex items-center justify-center rounded-md bg-primary-red px-6 py-3 text-white font-medium transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        >
                                            Contact Us
                                        </a>
                                        <a 
                                            href="/tutorial" 
                                            className="inline-flex items-center justify-center rounded-md border border-input bg-background px-6 py-3 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        >
                                            View Tutorial
                                        </a>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </Container>
                </main>
        </div>
    );
}

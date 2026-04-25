import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, H3, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Contact Us - Scenariaid',
    description: 'Get in touch with the Scenariaid team for support, partnerships, and inquiries.',
};

export default function ContactPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Contact Us" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Hero Section */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-blue">Get in Touch</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                We'd love to hear from you! Whether you have questions, 
                                feedback, or partnership opportunities, our team is here to help.
                            </P>
                        </div>

                        <div className="grid md:grid-cols-2 gap-12 mb-12">
                            {/* Contact Form */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Send us a Message</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <form className="space-y-6">
                                        <div>
                                            <label htmlFor="name" className="block text-sm font-medium mb-2">
                                                Name
                                            </label>
                                            <input
                                                type="text"
                                                id="name"
                                                name="name"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                placeholder="Your name"
                                                required
                                            />
                                        </div>
                                        
                                        <div>
                                            <label htmlFor="email" className="block text-sm font-medium mb-2">
                                                Email
                                            </label>
                                            <input
                                                type="email"
                                                id="email"
                                                name="email"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                placeholder="your.email@example.com"
                                                required
                                            />
                                        </div>
                                        
                                        <div>
                                            <label htmlFor="subject" className="block text-sm font-medium mb-2">
                                                Subject
                                            </label>
                                            <input
                                                type="text"
                                                id="subject"
                                                name="subject"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                placeholder="How can we help you?"
                                                required
                                            />
                                        </div>
                                        
                                        <div>
                                            <label htmlFor="message" className="block text-sm font-medium mb-2">
                                                Message
                                            </label>
                                            <textarea
                                                id="message"
                                                name="message"
                                                rows={5}
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 resize-none"
                                                placeholder="Tell us more about your inquiry..."
                                                required
                                            />
                                        </div>
                                        
                                        <button
                                            type="submit"
                                            className="w-full bg-primary-red text-white py-3 px-4 rounded-md font-medium transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        >
                                            Send Message
                                        </button>
                                    </form>
                                </CardContent>
                            </Card>

                            {/* Contact Information */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Other Ways to Reach Us</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    <div className="flex items-center space-x-3">
                                        <div className="w-8 h-8 bg-primary-red rounded-full flex items-center justify-center">
                                                <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 4.26a42 42 0 01.58.55.42.42L4.83 12.4a2 2 0 011-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 012-2h2a2 2 0 012-2V6a2 2 0 01-2-2H3a2 2 0 01-2 2v4a2 2 0 012 2h2a2 2 0 012 2V6a2 2 0 01-2-2H3z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8 12 4.5-4.5 4.5M8 12l4.5 4.5" />
                                                </svg>
                                            </div>
                                        <div>
                                            <H3 className="font-semibold">Email</H3>
                                            <P className="text-muted-foreground">hello@scenariaid.com</P>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center space-x-3">
                                        <div className="w-8 h-8 bg-primary-blue rounded-full flex items-center justify-center">
                                                <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H3z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m7 8h3m-3 4h3m-6 4h3" />
                                                </svg>
                                            </div>
                                        <div>
                                            <H3 className="font-semibold">Phone</H3>
                                            <P className="text-muted-foreground">+1 (555) 123-4567</P>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center space-x-3">
                                        <div className="w-8 h-8 bg-primary-light rounded-full flex items-center justify-center">
                                                <svg className="w-4 h-4 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-.463-.456L4.34 12.228a1 1 0 010.087-.998l.069 1.456c.079.341.23.933.654.646.987l.069 1.456a1 1 0 01.087.998l-.069 1.456c-.079-.341-.23-.933-.654-.646-.987l-.069-1.456a1 1 0 01-.087-.998L16.657 16.657z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01" />
                                                </svg>
                                            </div>
                                        <div>
                                            <H3 className="font-semibold">Office</H3>
                                            <P className="text-muted-foreground">Mon-Fri, 9AM-5PM PST</P>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Response Time */}
                        <Card className="bg-primary-light">
                            <CardHeader>
                                <CardTitle>Response Time</CardTitle>
                            </CardHeader>
                            <CardContent className="text-center">
                                    <P className="text-lg">
                                        We typically respond to inquiries within <strong className="text-primary-red">24 hours</strong> 
                                        during business days. For urgent matters, please call us directly.
                                    </P>
                            </CardContent>
                        </Card>
                    </div>
                </Container>
            </main>
        </div>
    );
}

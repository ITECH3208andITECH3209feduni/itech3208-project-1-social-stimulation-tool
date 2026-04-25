import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, H3, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Account Security - Scenariaid',
    description: 'Manage your Scenariaid account security settings and authentication preferences.',
};

export default function SecurityPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Account Security" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Header */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-red">Account Security</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Keep your account secure with our comprehensive security features 
                                and authentication settings.
                            </P>
                        </div>

                        <div className="grid md:grid-cols-2 gap-8 mb-12">
                            {/* Two-Factor Authentication */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Two-Factor Authentication</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div>
                                            <label htmlFor="2fa-enabled" className="flex items-center space-x-3">
                                                <input
                                                    type="checkbox"
                                                    id="2fa-enabled"
                                                    defaultChecked={true}
                                                    className="h-4 w-4 text-primary-blue border-primary-blue focus:ring-2 focus:ring-primary-blue"
                                                />
                                                <span className="text-sm font-medium">Enable 2FA</span>
                                            </label>
                                            <P className="text-sm text-muted-foreground ml-7">
                                                Add an extra layer of security to your account
                                            </P>
                                        </div>
                                        
                                        <div>
                                            <label htmlFor="2fa-method" className="block text-sm font-medium mb-2">
                                                2FA Method
                                            </label>
                                            <select
                                                id="2fa-method"
                                                defaultValue="app"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            >
                                                <option value="app">Authenticator App</option>
                                                <option value="sms">SMS</option>
                                                <option value="email">Email</option>
                                            </select>
                                        </div>
                                        
                                        <button className="w-full bg-primary-blue text-white py-3 px-4 rounded-md font-medium transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                            Setup 2FA
                                        </button>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Active Sessions */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Active Sessions</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="border rounded-lg p-4">
                                            <div className="flex justify-between items-start mb-2">
                                                <div>
                                                    <P className="font-medium">Current Session</P>
                                                    <P className="text-sm text-muted-foreground">Windows 11 • Chrome Browser • San Francisco, CA</P>
                                                </div>
                                                <button className="text-primary-red hover:underline text-sm">Terminate</button>
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                <P>Started: April 25, 2026 at 3:15 PM PST</P>
                                                <P>Last Activity: 2 minutes ago</P>
                                                <P>IP Address: 192.168.1.100</P>
                                            </div>
                                        </div>
                                        
                                        <div className="border rounded-lg p-4">
                                            <div className="flex justify-between items-start mb-2">
                                                <div>
                                                    <P className="font-medium">Mobile Session</P>
                                                    <P className="text-sm text-muted-foreground">iPhone 14 • Safari Browser • New York, NY</P>
                                                </div>
                                                <button className="text-primary-red hover:underline text-sm">Terminate</button>
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                <P>Started: April 24, 2026 at 10:30 AM PST</P>
                                                <P>Last Activity: 1 hour ago</P>
                                                <P>IP Address: 192.168.1.101</P>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div className="flex justify-between items-center">
                                        <button className="text-primary-blue hover:underline">View All Sessions</button>
                                        <button className="bg-primary-red text-white px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-red-700">
                                            Sign Out All Devices
                                        </button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Security Recommendations */}
                        <Card className="bg-primary-light">
                            <CardHeader>
                                <CardTitle>Security Recommendations</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <H3 className="text-lg font-semibold mb-2">Strong Password</H3>
                                        <P className="text-sm text-muted-foreground">
                                            Use a unique password with at least 12 characters, 
                                            including uppercase, lowercase, numbers, and symbols.
                                        </P>
                                    </div>
                                    <div>
                                        <H3 className="text-lg font-semibold mb-2">Regular Updates</H3>
                                        <P className="text-sm text-muted-foreground">
                                            Keep your software and browsers updated to the latest 
                                            versions for optimal security.
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

import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Account - Scenariaid',
    description: 'Manage your Scenariaid account settings, profile, and preferences.',
};

export default function AccountPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Account" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Header */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-blue">My Account</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Manage your account settings, profile information, and preferences 
                                all in one convenient location.
                            </P>
                        </div>

                        <div className="grid md:grid-cols-3 gap-8 mb-12">
                            {/* Profile Section */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Profile</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div>
                                            <label htmlFor="name" className="block text-sm font-medium mb-2">
                                                Full Name
                                            </label>
                                            <input
                                                type="text"
                                                id="name"
                                                defaultValue="John Doe"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="email" className="block text-sm font-medium mb-2">
                                                Email Address
                                            </label>
                                            <input
                                                type="email"
                                                id="email"
                                                defaultValue="john.doe@example.com"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="username" className="block text-sm font-medium mb-2">
                                                Username
                                            </label>
                                            <input
                                                type="text"
                                                id="username"
                                                defaultValue="johndoe"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            />
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Settings Section */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Settings</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div>
                                            <label htmlFor="notifications" className="flex items-center space-x-3">
                                                <input
                                                    type="checkbox"
                                                    id="notifications"
                                                    defaultChecked={true}
                                                    className="h-4 w-4 text-primary-red border-primary-red focus:ring-2 focus:ring-primary-red"
                                                />
                                                <span className="text-sm font-medium">Email Notifications</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label htmlFor="newsletter" className="flex items-center space-x-3">
                                                <input
                                                    type="checkbox"
                                                    id="newsletter"
                                                    defaultChecked={false}
                                                    className="h-4 w-4 text-primary-blue border-primary-blue focus:ring-2 focus:ring-primary-blue"
                                                />
                                                <span className="text-sm font-medium">Newsletter Subscription</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label htmlFor="theme" className="block text-sm font-medium mb-2">
                                                Theme Preference
                                            </label>
                                            <select
                                                id="theme"
                                                defaultValue="light"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            >
                                                <option value="light">Light</option>
                                                <option value="dark">Dark</option>
                                                <option value="system">System</option>
                                            </select>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Security Section */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Security</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div>
                                            <label htmlFor="current-password" className="block text-sm font-medium mb-2">
                                                Current Password
                                            </label>
                                            <input
                                                type="password"
                                                id="current-password"
                                                placeholder="Enter current password"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="new-password" className="block text-sm font-medium mb-2">
                                                New Password
                                            </label>
                                            <input
                                                type="password"
                                                id="new-password"
                                                placeholder="Enter new password"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="confirm-password" className="block text-sm font-medium mb-2">
                                                Confirm New Password
                                            </label>
                                            <input
                                                type="password"
                                                id="confirm-password"
                                                placeholder="Confirm new password"
                                                className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            className="w-full bg-primary-red text-white py-3 px-4 rounded-md font-medium transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        >
                                            Update Password
                                        </button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Action Buttons */}
                        <div className="grid md:grid-cols-2 gap-8 mb-12">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Subscription</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <P className="font-medium">Current Plan</P>
                                                <P className="text-2xl font-bold text-primary-blue">Professional</P>
                                            </div>
                                            <button className="text-primary-blue hover:underline">
                                                Upgrade Plan
                                            </button>
                                        </div>
                                        <div className="pt-4 border-t">
                                            <P className="text-sm text-muted-foreground mb-2">
                                                Next billing date: <strong>May 25, 2026</strong>
                                            </P>
                                            <P className="text-sm text-muted-foreground">
                                                Auto-renewal is <strong className="text-green-600">enabled</strong>
                                            </P>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Danger Zone</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <P className="text-sm text-muted-foreground mb-4">
                                            <strong className="text-red-600">Warning:</strong> These actions are irreversible. 
                                            Please proceed with caution.
                                        </P>
                                        <div className="space-y-3">
                                            <button className="w-full border border-red-300 text-red-600 hover:bg-red-50 py-2 px-4 rounded-md font-medium transition-colors">
                                                Delete Account
                                            </button>
                                            <button className="w-full border border-red-300 text-red-600 hover:bg-red-50 py-2 px-4 rounded-md font-medium transition-colors">
                                                Export Data
                                            </button>
                                        </div>
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

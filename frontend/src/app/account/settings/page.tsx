import { Metadata } from 'next';
import { Container } from '@/components/layout/container';
import { Header } from '@/components/layout/header';
import { H1, H2, P, Card, CardContent, CardHeader, CardTitle } from '@/components/ui';

export const metadata: Metadata = {
    title: 'Account Settings - Scenariaid',
    description: 'Manage your Scenariaid account settings and preferences.',
};

export default function SettingsPage() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Account Settings" />
            
            <main className="py-20">
                <Container>
                    <div className="max-w-4xl mx-auto">
                        {/* Header */}
                        <div className="text-center mb-16">
                            <H1 className="mb-4 text-primary-blue">Account Settings</H1>
                            <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Manage your account preferences, notification settings, 
                                and personalization options.
                            </P>
                        </div>

                        {/* Settings Sections */}
                        <div className="grid md:grid-cols-2 gap-8">
                            {/* General Settings */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>General Settings</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    <div>
                                        <label htmlFor="language" className="block text-sm font-medium mb-2">
                                            Language Preference
                                        </label>
                                        <select
                                            id="language"
                                            defaultValue="en"
                                            className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                        >
                                            <option value="en">English</option>
                                            <option value="vi">Tiếng Việt</option>
                                            <option value="es">Español</option>
                                            <option value="fr">Français</option>
                                            <option value="de">Deutsch</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label htmlFor="timezone" className="block text-sm font-medium mb-2">
                                            Timezone
                                        </label>
                                        <select
                                            id="timezone"
                                            defaultValue="UTC"
                                            className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                        >
                                            <option value="UTC">UTC</option>
                                            <option value="PST">PST (UTC-8)</option>
                                            <option value="EST">EST (UTC-5)</option>
                                            <option value="CST">CST (UTC-6)</option>
                                            <option value="MST">MST (UTC-7)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label htmlFor="dateFormat" className="block text-sm font-medium mb-2">
                                            Date Format
                                        </label>
                                        <select
                                            id="dateFormat"
                                            defaultValue="MM/DD/YYYY"
                                            className="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-background"
                                        >
                                            <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                            <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                            <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                        </select>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Privacy Settings */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Privacy Settings</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    <div>
                                        <label htmlFor="profileVisibility" className="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                id="profileVisibility"
                                                defaultChecked={true}
                                                className="h-4 w-4 text-primary-blue border-primary-blue focus:ring-2 focus:ring-primary-blue"
                                            />
                                            <span className="text-sm font-medium">Public Profile</span>
                                        </label>
                                        <P className="text-sm text-muted-foreground ml-7">
                                            Make your profile visible to other users
                                        </P>
                                    </div>
                                    
                                    <div>
                                        <label htmlFor="showEmail" className="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                id="showEmail"
                                                defaultChecked={false}
                                                className="h-4 w-4 text-primary-blue border-primary-blue focus:ring-2 focus:ring-primary-blue"
                                            />
                                            <span className="text-sm font-medium">Show Email Address</span>
                                        </label>
                                        <P className="text-sm text-muted-foreground ml-7">
                                            Display email address on your public profile
                                        </P>
                                    </div>
                                    
                                    <div>
                                        <label htmlFor="activityStatus" className="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                id="activityStatus"
                                                defaultChecked={true}
                                                className="h-4 w-4 text-primary-blue border-primary-blue focus:ring-2 focus:ring-primary-blue"
                                            />
                                            <span className="text-sm font-medium">Activity Status</span>
                                        </label>
                                        <P className="text-sm text-muted-foreground ml-7">
                                            Show when you're online and active
                                        </P>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Save Button */}
                        <Card className="bg-primary-light">
                            <CardContent className="text-center py-6">
                                <button className="bg-primary-red text-white px-6 py-3 rounded-md font-medium transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Save Settings
                                </button>
                            </CardContent>
                        </Card>
                    </div>
                </Container>
            </main>
        </div>
    );
}

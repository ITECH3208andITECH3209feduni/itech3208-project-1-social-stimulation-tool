import { Header } from "../components/layout/header";
import { Container } from "../components/layout/container";
import { Button, Card, CardContent, CardDescription, CardHeader, CardTitle, H1, H2, P, Badge, Input } from "../components/ui";

export default function Home() {
  return (
    <div className="min-h-screen bg-background">
      <Header title="Scenariaid" />
      
      <main className="py-20">
        <Container>
          <div className="text-center mb-16">
            <H1 className="mb-4 text-primary-red">Welcome to Scenariaid</H1>
            <P className="text-xl text-muted-foreground max-w-2xl mx-auto">
              A modern, scalable, and maintainable frontend application built with Next.js, TypeScript, and Tailwind CSS.
            </P>
            <div className="mt-8 flex gap-4 justify-center">
              <Button variant="red" size="lg">Get Started</Button>
              <Button variant="outline" size="lg">Learn More</Button>
            </div>
          </div>

          <div className="grid md:grid-cols-3 gap-8 mb-16">
            <Card>
              <CardHeader>
                <CardTitle className="text-primary-blue">Easy to Use</CardTitle>
                <CardDescription>
                  Simple and intuitive components that make development faster
                </CardDescription>
              </CardHeader>
              <CardContent>
                <P>Our component library is designed with developers in mind, providing reusable and customizable UI elements.</P>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-primary-red">Scalable</CardTitle>
                <CardDescription>
                  Built to grow with your application needs
                </CardDescription>
              </CardHeader>
              <CardContent>
                <P>Modular architecture and clean code patterns ensure your application can scale effortlessly.</P>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-primary-light">Modern Design</CardTitle>
                <CardDescription>
                  Beautiful UI with consistent design system
                </CardDescription>
              </CardHeader>
              <CardContent>
                <P>Clean, modern interface with carefully chosen colors and typography for optimal user experience.</P>
              </CardContent>
            </Card>
          </div>

          <div className="space-y-8">
            <div>
              <H2 className="mb-4">Component Showcase</H2>
              <div className="grid md:grid-cols-2 gap-8">
                <Card>
                  <CardHeader>
                    <CardTitle>Buttons</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <div className="flex flex-wrap gap-2">
                      <Button variant="default">Default</Button>
                      <Button variant="red">Red</Button>
                      <Button variant="blue">Blue</Button>
                      <Button variant="light">Light</Button>
                      <Button variant="outline">Outline</Button>
                    </div>
                  </CardContent>
                </Card>

                <Card>
                  <CardHeader>
                    <CardTitle>Badges</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <div className="flex flex-wrap gap-2">
                      <Badge>Default</Badge>
                      <Badge variant="red">Red</Badge>
                      <Badge variant="blue">Blue</Badge>
                      <Badge variant="light">Light</Badge>
                      <Badge variant="outline">Outline</Badge>
                    </div>
                  </CardContent>
                </Card>

                <Card>
                  <CardHeader>
                    <CardTitle>Input Fields</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <Input placeholder="Enter your email..." />
                    <Input type="password" placeholder="Enter your password..." />
                  </CardContent>
                </Card>

                <Card>
                  <CardHeader>
                    <CardTitle>Typography</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-2">
                    <P className="text-primary-red">Red text example</P>
                    <P className="text-primary-blue">Blue text example</P>
                    <P className="text-primary-light">Light text example</P>
                  </CardContent>
                </Card>
              </div>
            </div>
          </div>
        </Container>
      </main>
    </div>
  );
}

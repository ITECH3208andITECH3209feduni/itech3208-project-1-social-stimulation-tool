import { BrowserRouter as Router } from "react-router-dom";
import AppRoutes from "./AppRoutes";
import { Toaster } from "@/components/ui/toaster";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

const queryClient = new QueryClient();

function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <Router>
                <AppRoutes />
                <Toaster />
            </Router>
        </QueryClientProvider>
    );
}

export default App;

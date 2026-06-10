import { BrowserRouter as Router } from "react-router-dom";
import AppRoutes from "./AppRoutes";
import { Toaster } from "@/components/ui/toaster";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import FeedbackPopup from "@/components/common/popups/FeedbackPopup";
import useFeedback from "@/hooks/custom-hooks/useFeedback";

const queryClient = new QueryClient();

function AppContent() {
    const { open, setOpen, dismisPopup } = useFeedback();
    return (
        <>
            <AppRoutes />
            <Toaster />
            <FeedbackPopup open={open} setOpen={setOpen} dismisPopup={dismisPopup} />
        </>
    );
}

function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <Router>
                <AppContent />
            </Router>
        </QueryClientProvider>
    );
}

export default App;

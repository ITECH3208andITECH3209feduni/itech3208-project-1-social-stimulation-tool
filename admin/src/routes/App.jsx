import { BrowserRouter as Router } from "react-router-dom";
import AppRoutes from "./AppRoutes";
import { Toaster } from "@/components/ui/toaster";
import { Spinner } from "@chakra-ui/react";

function App() {
    return (
        <Router>
            <AppRoutes />
            <Toaster />
        </Router>
    );
}

export default App;

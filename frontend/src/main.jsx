import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { ChakraProvider } from "@chakra-ui/react";
import "./index.css";
import system from "@/theme/index.js";
import App from "./routes/App.jsx";

createRoot(document.getElementById("root")).render(
    <StrictMode>
        <ChakraProvider value={system}>
            <App />
        </ChakraProvider>
    </StrictMode>,
);

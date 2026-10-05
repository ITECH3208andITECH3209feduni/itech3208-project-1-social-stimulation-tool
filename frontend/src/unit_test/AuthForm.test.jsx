import React from "react";
import { render, screen, fireEvent } from "@testing-library/react";
import { describe, it, expect, vi, beforeEach } from "vitest";
import AuthForm from "../components/auth/AuthForm";
import { MemoryRouter } from "react-router-dom";
import { ChakraProvider, defaultSystem } from "@chakra-ui/react";
import { toaster } from "@/components/ui/toaster";

// Mock the toaster to prevent actual toasts from firing and to check if they were called
vi.mock("@/components/ui/toaster", () => ({
    toaster: {
        create: vi.fn(),
    },
}));

// Mock SocialLogin to simplify the test tree since it's not the focus
vi.mock("@/components/auth/SocialLogin", () => ({
    default: () => <div data-testid="social-login-mock" />
}));

const loginFields = [
    { label: "Email Address", name: "email", type: "email", placeholder: "example@scenariaid.com" },
    { label: "Password", name: "password", type: "password", placeholder: "Enter your password" },
];

const registerFields = [
    { label: "Full Name", name: "name", type: "text", placeholder: "John Doe" },
    { label: "Email Address", name: "email", type: "email", placeholder: "example@scenariaid.com" },
    { label: "Password", name: "password", type: "password", placeholder: "Enter your password" },
];

const renderComponent = (fields, onSubmit) => {
    return render(
        <ChakraProvider value={defaultSystem}>
            <MemoryRouter>
                <AuthForm fields={fields} onSubmit={onSubmit} />
            </MemoryRouter>
        </ChakraProvider>
    );
};

describe("AuthForm Component", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe("Login Flow", () => {
        it("should render login fields correctly", () => {
            renderComponent(loginFields, vi.fn());
            expect(screen.getByText(/Nice to see you again!/i)).toBeInTheDocument();
            expect(screen.getByPlaceholderText("example@scenariaid.com")).toBeInTheDocument();
            expect(screen.getByPlaceholderText("Enter your password")).toBeInTheDocument();
            expect(screen.getByRole("button", { name: /Sign in/i })).toBeInTheDocument();
        });

        it("should call onSubmit with input data when sign in is clicked", () => {
            const mockSubmit = vi.fn();
            renderComponent(loginFields, mockSubmit);

            fireEvent.change(screen.getByPlaceholderText("example@scenariaid.com"), {
                target: { value: "test@example.com" },
            });
            fireEvent.change(screen.getByPlaceholderText("Enter your password"), {
                target: { value: "password123" },
            });

            fireEvent.click(screen.getByRole("button", { name: /Sign in/i }));

            expect(mockSubmit).toHaveBeenCalledTimes(1);
            expect(mockSubmit).toHaveBeenCalledWith({
                email: "test@example.com",
                password: "password123",
            });
            expect(toaster.create).not.toHaveBeenCalled();
        });
    });

    describe("Register Flow", () => {
        it("should render register fields correctly", () => {
            renderComponent(registerFields, vi.fn());
            expect(screen.getByText(/Welcome to our Scenario aid system!/i)).toBeInTheDocument();
            expect(screen.getByPlaceholderText("John Doe")).toBeInTheDocument();
            expect(screen.getByPlaceholderText("example@scenariaid.com")).toBeInTheDocument();
            expect(screen.getByPlaceholderText("Enter your password")).toBeInTheDocument();
            expect(screen.getByRole("button", { name: /Sign up/i })).toBeInTheDocument();
        });

        describe("Password Requirements Validations", () => {
            const testCases = [
                {
                    name: "fails all requirements (empty)",
                    password: "",
                    expectedErrors: [
                        "at least 8 characters",
                        "at least one uppercase letter",
                        "at least one lowercase letter",
                        "at least one number",
                        "at least one special character"
                    ],
                },
                {
                    name: "fails length, but has others",
                    password: "Aa1!",
                    expectedErrors: ["at least 8 characters"],
                },
                {
                    name: "fails uppercase",
                    password: "password123!",
                    expectedErrors: ["at least one uppercase letter"],
                },
                {
                    name: "fails lowercase",
                    password: "PASSWORD123!",
                    expectedErrors: ["at least one lowercase letter"],
                },
                {
                    name: "fails number",
                    password: "Password!@#",
                    expectedErrors: ["at least one number"],
                },
                {
                    name: "fails special character",
                    password: "Password1234",
                    expectedErrors: ["at least one special character"],
                },
                {
                    name: "meets all requirements",
                    password: "Password123!",
                    expectedErrors: [],
                }
            ];

            testCases.forEach(({ name, password, expectedErrors }) => {
                it(`should validate password requirements: ${name}`, () => {
                    const mockSubmit = vi.fn();
                    renderComponent(registerFields, mockSubmit);
                    
                    const passwordInput = screen.getByPlaceholderText("Enter your password");
                    if (password !== undefined) {
                        fireEvent.change(passwordInput, { target: { value: password } });
                    }
                    
                    const submitBtn = screen.getByRole("button", { name: /Sign up/i });
                    fireEvent.click(submitBtn);

                    if (expectedErrors.length > 0) {
                        expect(mockSubmit).not.toHaveBeenCalled();
                        expect(toaster.create).toHaveBeenCalledTimes(1);
                        
                        const callArg = toaster.create.mock.calls[0][0];
                        expectedErrors.forEach(err => {
                            expect(callArg.description).toContain(err);
                        });
                    } else {
                        expect(toaster.create).not.toHaveBeenCalled();
                        expect(mockSubmit).toHaveBeenCalledTimes(1);
                        expect(mockSubmit).toHaveBeenCalledWith(expect.objectContaining({
                            password: password
                        }));
                    }
                });
            });
        });
    });
});

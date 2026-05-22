import { useNavigate } from "react-router-dom";
import { Toaster, toaster } from "@/components/ui/toaster";
import { loginFields } from "@/components/auth/authConfig";
import AuthForm from "@/components/auth/AuthForm";
import useLogin from "@/hooks/custom-hooks/useLogin";

function LoginPage() {
    const { login } = useLogin();
    const navigate = useNavigate();
    const handleLogin = async (input) => {
        await login(
            {
                username: input.username,
                password: input.password,
            },
            {
                onSuccess: (_, msg) => {
                    toaster.create({
                        description: msg,
                        type: "success",
                        duration: 3000,
                    });
                    setTimeout(() => {
                        navigate("/");
                    }, 3100);
                },
                onError: (msg) => {
                    toaster.create({
                        description: msg,
                        type: "error",
                        duration: 3000,
                    });
                },
            },
        );
    };

    return (
        <>
            <AuthForm fields={loginFields} onSubmit={handleLogin} />
            <Toaster />
        </>
    );
}

export default LoginPage;

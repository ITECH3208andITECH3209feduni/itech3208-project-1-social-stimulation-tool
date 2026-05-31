import { registerFields } from "@/components/auth/authConfig";
import AuthForm from "@/components/auth/AuthForm";
import { Float, Spinner } from "@chakra-ui/react";
import { toaster } from "@/components/ui/toaster";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import useRegister from "@/hooks/custom-hooks/useRegister";

function RegisterPage() {
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();
    const { register } = useRegister();

    const handleRegsiter = async (inputs) => {
        await register(
            {
                username: inputs.username,
                email: inputs.email,
                phone: inputs.phone,
                password: inputs.password,
                confirmedPassword: inputs.confirmedPassword,
            },
            {
                onSuccess: (_, msg) => {
                    setLoading(false);
                    toaster.create({
                        description: msg,
                        type: "success",
                        duration: 3000,
                    });
                    navigate("/account/login");
                },
                onError: (msg) => {
                    setLoading(false);
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
            <AuthForm fields={registerFields} onSubmit={handleRegsiter} />
            {loading && (
                <Float placement={"center"}>
                    <Spinner color={"red"} size={"md"} />
                </Float>
            )}
        </>
    );
}

export default RegisterPage;

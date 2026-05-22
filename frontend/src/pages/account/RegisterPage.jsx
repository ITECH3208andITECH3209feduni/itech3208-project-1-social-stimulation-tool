import { registerFields } from "@/components/auth/authConfig";
import AuthForm from "@/components/auth/AuthForm";

function RegisterPage() {
    return <AuthForm fields={registerFields} />;
}

export default RegisterPage;

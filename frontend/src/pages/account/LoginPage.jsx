import { federationLogo } from "@/assets";
import AuthForm from "@/components/auth/AuthForm";

function LoginPage() {
    const fedUniLogo = {federationLogo}
    return <AuthForm isRegister={false} />;
}

export default LoginPage;

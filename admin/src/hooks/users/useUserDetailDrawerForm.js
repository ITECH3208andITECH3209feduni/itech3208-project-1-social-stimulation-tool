import { useState, useEffect } from "react";

const useUserDetailDrawerForm = (user, isOpen, onSave) => {
    const [accountStatus, setAccountStatus] = useState("active");

    useEffect(() => {
        if (isOpen && user) {
            setAccountStatus(user.accountStatus || "active");
        } else if (!isOpen) {
            setAccountStatus("active");
        }
    }, [isOpen, user]);

    const handleSave = () => {
        const payload = {
            accountStatus,
        };
        onSave(payload);
    };

    return {
        status: accountStatus,
        setStatus: setAccountStatus,
        handleSave,
    };
};

export default useUserDetailDrawerForm;

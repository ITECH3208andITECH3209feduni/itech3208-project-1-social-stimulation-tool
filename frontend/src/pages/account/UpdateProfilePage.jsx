import ProfileForm from "@/components/forms/ProfileForm";
import { toaster } from "@/components/ui/toaster";
import useUpdateUserProfile from "@/hooks/custom-hooks/useUpdateUserProfile";
import useUserProfile from "@/hooks/custom-hooks/useUserProfile";
import React from "react";

function UpdateProfilePage() {
    const { updateProfile } = useUpdateUserProfile();
    const { data: user, isLoading } = useUserProfile();

    const handleUpdateProfile = async (inputs) => {
        const payload = {
            firstName: inputs.firstName,
            lastName: inputs.lastName,
            email: inputs.email,
            location: inputs.location,
        };

        await updateProfile(payload, {
            onSuccess: (_, msg) => {
                toaster.create({
                    description: msg,
                    type: "success",
                });
            },
            onError: (msg) => {
                toaster.create({
                    description: msg,
                    type: "error",
                });
            },
        });
    };

    if (isLoading) {
        return null; // Can be replaced with a Spinner if needed
    }

    if (!user) {
        return null;
    }

    return <ProfileForm user={user} onSubmit={handleUpdateProfile} />;
}

export default UpdateProfilePage;

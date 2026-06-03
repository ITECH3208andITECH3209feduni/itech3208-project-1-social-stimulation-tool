import ContactForm from "@/components/forms/ContactForm";
import { Spinner, Flex, Box, Float } from "@chakra-ui/react";
import { toaster } from "@/components/ui/toaster";
import useSendContact from "@/hooks/custom-hooks/useSendContact";
import useUserProfile from "@/hooks/custom-hooks/useUserProfile";

function ContactPage() {
    const { data: user, isLoading } = useUserProfile();
    const { sendContact, loading } = useSendContact();

    const handleSendContact = async (inputs) => {
        const payload = {
            categoryId: inputs.categoryId,
            message: inputs.message,
        };
        const finished = await sendContact(payload, {
            onSuccess: (_, msg) => {
                toaster.create({
                    description: msg,
                    type: "success",
                    duration: 5000,
                });
            },
            onError: (msg) => {
                toaster.create({
                    description: msg,
                    type: "error",
                });
            },
        });
        return finished;
    };

    if (isLoading) {
        return (
            <Flex justify="center" align="center" h="100vh">
                <Spinner size="xl" />
            </Flex>
        );
    }

    return (
        <Box position="relative">
            <ContactForm user={user} onSubmit={handleSendContact} />
            {loading && (
                <Flex
                    position="absolute"
                    top="0"
                    left="0"
                    right="0"
                    bottom="0"
                    justify="center"
                    align="center"
                    zIndex="overlay"
                >
                    <Spinner size="xl" />
                </Flex>
            )}
        </Box>
    );
}

export default ContactPage;

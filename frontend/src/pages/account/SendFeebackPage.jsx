import { Spinner, Flex, Box } from "@chakra-ui/react";
import { toaster } from "@/components/ui/toaster";
import FeedbackForm from "@/components/forms/FeedbackForm";
import useFeedback from "@/hooks/custom-hooks/useFeedback";

function SendFeedbackPage() {
    const { sendFeedback, loading } = useFeedback();

    const handleSendFeedback = async (inputs) => {
        const payload = {
            content: inputs.content,
            rating: inputs.rating,
        };
        const finished = await sendFeedback(payload, {
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

    return (
        <Box position="relative">
            <FeedbackForm onSubmit={handleSendFeedback} />
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

export default SendFeedbackPage;

import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Button, Box, Text, VStack, HStack, CloseButton, Heading } from "@chakra-ui/react";

const DELAY_MS = 30_000;
const SESSION_KEY = "feedbackPopupSeen";

function FeedbackPopup() {
    const [open, setOpen] = useState(false);
    const navigate = useNavigate();

    useEffect(() => {
        if (sessionStorage.getItem(SESSION_KEY)) return;

        const timer = setTimeout(() => {
            setOpen(true);
            sessionStorage.setItem(SESSION_KEY, "true");
        }, DELAY_MS);

        return () => clearTimeout(timer);
    }, []);

    const handleFeedback = () => {
        setOpen(false);
        navigate("/contact");
    };

    if (!open) return null;

    return (
        <Box
            position="fixed"
            bottom="24px"
            right="24px"
            zIndex="popover"
            bg="white"
            boxShadow="lg"
            borderRadius="md"
            p={5}
            w="320px"
        >
            <HStack justify="space-between" mb={3}>
                <Heading fontFamily="Sora" color="brand.500" fontSize="md">
                    Enjoying Scenari-Aid?
                </Heading>
                <CloseButton size="sm" onClick={() => setOpen(false)} />
            </HStack>
            <VStack align="start" gap={4}>
                <Text fontFamily="Sora" fontSize="sm">
                    We&apos;d love to hear what you think! Your feedback helps us improve the
                    experience for everyone.
                </Text>
                <HStack justify="flex-end" w="full" gap={3}>
                    <Button variant="ghost" fontFamily="Sora" onClick={() => setOpen(false)}>
                        Maybe Later
                    </Button>
                    <Button bg="brand.500" color="white" fontFamily="Sora" onClick={handleFeedback}>
                        Give Feedback
                    </Button>
                </HStack>
            </VStack>
        </Box>
    );
}

export default FeedbackPopup;

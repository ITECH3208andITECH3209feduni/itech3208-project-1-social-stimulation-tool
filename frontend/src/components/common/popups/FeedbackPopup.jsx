import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import {
    Button,
    Dialog,
    Portal,
    Text,
    VStack,
    HStack,
    CloseButton,
} from "@chakra-ui/react";

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

    return (
        <Dialog.Root open={open} onOpenChange={({ open }) => setOpen(open)} size="sm">
            <Portal>
                <Dialog.Positioner
                    style={{
                        position: "fixed",
                        bottom: "24px",
                        right: "24px",
                        top: "auto",
                        left: "auto",
                        alignItems: "flex-end",
                        justifyContent: "flex-end",
                    }}
                >
                    <Dialog.Content bg="white">
                        <Dialog.Header>
                            <Dialog.Title fontFamily="Sora" color="brand.500">
                                Enjoying Scenari-Aid?
                            </Dialog.Title>
                            <Dialog.CloseTrigger asChild>
                                <CloseButton size="sm" />
                            </Dialog.CloseTrigger>
                        </Dialog.Header>
                        <Dialog.Body>
                            <VStack align="start" gap={3}>
                                <Text fontFamily="Sora" fontSize="sm">
                                    We&apos;d love to hear what you think! Your feedback helps us
                                    improve the experience for everyone.
                                </Text>
                            </VStack>
                        </Dialog.Body>
                        <Dialog.Footer>
                            <HStack justify="flex-end" gap={3}>
                                <Button
                                    variant="ghost"
                                    fontFamily="Sora"
                                    onClick={() => setOpen(false)}
                                >
                                    Maybe Later
                                </Button>
                                <Button
                                    bg="brand.500"
                                    color="white"
                                    fontFamily="Sora"
                                    onClick={handleFeedback}
                                >
                                    Give Feedback
                                </Button>
                            </HStack>
                        </Dialog.Footer>
                    </Dialog.Content>
                </Dialog.Positioner>
            </Portal>
        </Dialog.Root>
    );
}

export default FeedbackPopup;

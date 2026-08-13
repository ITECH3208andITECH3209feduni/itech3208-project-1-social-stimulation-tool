import { Box, Text, Flex, Button, VStack, Spacer, CloseButton, Float } from "@chakra-ui/react";
import { useNavigate } from "react-router-dom";
import PropTypes from "prop-types";

function FeedbackPopup({ open, setOpen, dismisPopup }) {
    const navigate = useNavigate();

    if (!open) return null;

    const handleDismiss = () => {
        dismisPopup();
    };

    const handleShareFeedback = () => {
        setOpen(false);
        navigate("/account/send-feedback");
    };

    return (
        <Box
            position="fixed"
            bottom="12px"
            right="12px"
            bg="white"
            boxShadow="lg"
            borderRadius="md"
            p="4"
            w={"45%"}
            zIndex="9999"
            border="1px solid"
            borderColor="gray.200"
        >
            <Float offset="4">
                <CloseButton w="10px" h="10px" onClick={handleDismiss} />
            </Float>
            <VStack align={"start"}>
                <Text fontSize="18px" fontFamily="Sora" color="brand.500">
                    How is your experience with Scenari-Aid so far?
                </Text>

                <Text fontSize="14px" fontFamily="Sora" color="gray.600">
                    Your feedback helps us improve the platform and create a better learning
                    experience.
                </Text>
            </VStack>

            <Flex mt={"10px"}>
                <Spacer />
                <Flex justifyContent="flex-end" alignItems={"flex-end"} gap={2} w={"60%"}>
                    <Button
                        flex={1}
                        bg="gray.300"
                        color="brand.500"
                        variant="outline"
                        size="sm"
                        onClick={handleDismiss}
                    >
                        Maybe Later
                    </Button>
                    <Button
                        flex={1}
                        bg="skyblue.500"
                        color="white"
                        size="sm"
                        onClick={handleShareFeedback}
                    >
                        Share your feedback
                    </Button>
                </Flex>
            </Flex>
        </Box>
    );
}

FeedbackPopup.propTypes = {
    open: PropTypes.bool.isRequired,
    setOpen: PropTypes.func.isRequired,
    dismisPopup: PropTypes.func.isRequired,
};

export default FeedbackPopup;

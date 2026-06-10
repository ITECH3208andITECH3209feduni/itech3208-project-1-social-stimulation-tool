import RatingBar from "../common/ratingbar/RatingBar";
import { Box, Button, Field, Flex, Heading, Text, Textarea, VStack } from "@chakra-ui/react";
import React, { useState } from "react";

function FeedbackForm({ onSubmit }) {
    const [inputs, setInputs] = useState({
        content: "",
        rating: 0,
    });

    const handleInputChange = (key, value) => {
        setInputs((prev) => ({ ...prev, [key]: value }));
    };

    const resetForm = () => {
        setInputs((prev) => ({ ...prev, content: "", rating: 0 }));
    };

    const handleSubmit = async () => {
        const success = await onSubmit(inputs);
        if (success) resetForm();
    };

    return (
        <Flex
            position={"relative"}
            w={"100%"}
            p="8px"
            justify={"center"}
            alignItems={"center"}
            bg={"white"}
        >
            <Box>
                <Heading fontFamily={"Sora"} fontSize={48} color={"brand.500"} fontWeight={"bold"}>
                    Share Your Feedback
                </Heading>
                <Text>
                    We're continuously enhancing Scenari-Aid to create better learning experiences.
                    Share your thoughts and help shape the future of the platform.
                </Text>
                <Box
                    w={"100%"}
                    p={"40px"}
                    mt={"5"}
                    display={"flex"}
                    flexDir={"column"}
                    spaceY={"4"}
                    border={"solid"}
                    borderRadius={"10px"}
                >
                    {/* Rating field */}
                    <Flex>
                        <VStack align={"start"}>
                            <Text fontSize="sm" fontWeight="medium">
                                Your Rating
                            </Text>
                            <RatingBar
                                rate={inputs.rating}
                                isReadOnly={false}
                                onChange={(newRate) => handleInputChange("rating", newRate)}
                            />
                        </VStack>
                    </Flex>

                    {/* Message fields */}
                    <Field.Root>
                        <Field.Label>Your Feedback</Field.Label>
                        <Textarea
                            placeholder="Share your experience with Scenari-Aid, suggest improvements, or let us know about any issues you've encountered."
                            color={"black"}
                            bg={"gray.100"}
                            borderColor={"gray.400"}
                            value={inputs.content}
                            rows={5}
                            name="content"
                            onChange={(e) => handleInputChange("content", e.target.value)}
                        />
                    </Field.Root>

                    {/* Submit fields */}
                    <Box justifyContent={"center"}>
                        <Button w={"40%"} bg={"skyblue.500"} onClick={handleSubmit}>
                            <Text>Submit</Text>
                        </Button>
                    </Box>
                </Box>
            </Box>
        </Flex>
    );
}

export default FeedbackForm;

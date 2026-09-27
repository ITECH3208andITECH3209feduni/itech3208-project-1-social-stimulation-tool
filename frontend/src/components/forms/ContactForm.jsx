import {
    Box,
    Button,
    Field,
    Flex,
    Heading,
    Text,
    Textarea,
} from "@chakra-ui/react";
import React, { useState } from "react";
import NormalField from "../common/fields/NormalField";

function ContactForm({ user, onSubmit }) {
    const [inputs, setInputs] = useState({
        name: user?.name || "",
        email: user?.email || "",
        message: "",
    });

    const [errors, setErrors] = useState({});

    const handleInputChange = (key, value) => {
        setInputs((prev) => ({ ...prev, [key]: value }));

        setErrors((prev) => ({
            ...prev,
            [key]: "",
        }));
    };

    const validateForm = () => {
        const newErrors = {};

        if (!inputs.name.trim()) {
            newErrors.name = "Name is required";
        }

        if (!inputs.email.trim()) {
            newErrors.email = "Email is required";
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email)) {
            newErrors.email = "Please enter a valid email address";
        }

        if (!inputs.message.trim()) {
            newErrors.message = "Message is required";
        }

        setErrors(newErrors);

        return Object.keys(newErrors).length === 0;
    };

    const resetForm = () => {
        setInputs((prev) => ({
            ...prev,
            message: "",
        }));
        setErrors({});
    };

    const handleSubmit = async () => {
        if (!validateForm()) {
            return;
        }

        const success = await onSubmit(inputs);

        if (success) {
            resetForm();
        }
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
                <Heading
                    fontFamily={"Sora"}
                    fontSize={48}
                    color={"brand.500"}
                    fontWeight={"bold"}
                >
                    Contact Us
                </Heading>

                <Text>
                    Get in touch with our team to learn more about Scenario Aid
                    digital enhancment solutions and how they can upgrade your
                    skills.
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
                    <Heading
                        mt={"4"}
                        fontFamily={"Sora"}
                        fontSize={"30px"}
                        color={"black"}
                        fontWeight={"500"}
                        textAlign={"left"}
                    >
                        Send us a message
                    </Heading>

                    {/* Name and Email */}
                    <Flex gap={"4"}>
                        <Box flex="1">
                            <NormalField
                                fieldLabel={
                                    <>
                                        Name{" "}
                                        <Text as="span" color="red.500">
                                            *
                                        </Text>
                                    </>
                                }
                                inputPlaceholder="Your name"
                                name="name"
                                value={inputs.name}
                                onChange={(e) =>
                                    handleInputChange("name", e.target.value)
                                }
                            />

                            {errors.name && (
                                <Text color="red.500" fontSize="sm" mt="1">
                                    {errors.name}
                                </Text>
                            )}
                        </Box>

                        <Box flex="1">
                            <NormalField
                                fieldLabel={
                                    <>
                                        Email{" "}
                                        <Text as="span" color="red.500">
                                            *
                                        </Text>
                                    </>
                                }
                                inputPlaceholder="Your email"
                                type="email"
                                name="email"
                                value={inputs.email}
                                onChange={(e) =>
                                    handleInputChange("email", e.target.value)
                                }
                            />

                            {errors.email && (
                                <Text color="red.500" fontSize="sm" mt="1">
                                    {errors.email}
                                </Text>
                            )}
                        </Box>
                    </Flex>

                    {/* Message */}
                    <Field.Root>
                        <Field.Label>
                            Message{" "}
                            <Text as="span" color="red.500">
                                *
                            </Text>
                        </Field.Label>

                        <Textarea
                            placeholder="Tell us more about your needs and how we can help you..."
                            color={"black"}
                            bg={"gray.100"}
                            borderColor={"gray.400"}
                            value={inputs.message}
                            name="message"
                            onChange={(e) =>
                                handleInputChange("message", e.target.value)
                            }
                        />

                        {errors.message && (
                            <Text color="red.500" fontSize="sm" mt="1">
                                {errors.message}
                            </Text>
                        )}
                    </Field.Root>

                    {/* Submit */}
                    <Box justifyContent={"center"}>
                        <Button
                            w={"40%"}
                            bg={"skyblue.500"}
                            onClick={handleSubmit}
                        >
                            <Text>Submit</Text>
                        </Button>
                    </Box>
                </Box>
            </Box>
        </Flex>
    );
}

export default ContactForm;
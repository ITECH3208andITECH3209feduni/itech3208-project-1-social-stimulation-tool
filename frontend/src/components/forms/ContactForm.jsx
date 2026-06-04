import {
    Box,
    Button,
    Field,
    Flex,
    Heading,
    NativeSelect,
    Text,
    Textarea,
} from "@chakra-ui/react";
import React, { useState } from "react";
import NormalField from "../common/fields/NormalField";
import useCategories from "@/hooks/custom-hooks/useCategories";

function ContactForm({ user, onSubmit }) {
    const { categories } = useCategories();

    const [inputs, setInputs] = useState({
        firstName: user?.firstName || "",
        lastName: user?.lastName || "",
        email: user?.email || "",
        location: user?.location || "",
        categoryId: "",
        message: "",
    });

    const handleInputChange = (key, value) => {
        setInputs((prev) => ({ ...prev, [key]: value }));
    };

    const resetForm = () => {
        setInputs((prev) => ({ ...prev, categoryId: "", message: "" }));
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
                    Contact Us
                </Heading>
                <Text>
                    Get in touch with our team to learn more about Scenario Aid digital enhancment
                    solutions and how they can upgrade your skills.
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

                    {/* First and last name fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            fieldLabel="First name"
                            inputPlaceholder="Your first name"
                            name="firstName"
                            value={inputs.firstName}
                            onChange={(e) => handleInputChange("firstName", e.target.value)}
                        />
                        <NormalField
                            fieldLabel="Last name"
                            inputPlaceholder="Your last name"
                            name="lastName"
                            value={inputs.lastName}
                            onChange={(e) => handleInputChange("lastName", e.target.value)}
                        />
                    </Flex>

                    {/* Email and location fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            fieldLabel="Email"
                            inputPlaceholder="Your email"
                            type="email"
                            name="email"
                            value={inputs.email}
                            onChange={(e) => handleInputChange("email", e.target.value)}
                        />
                        <NormalField
                            fieldLabel="Location"
                            inputPlaceholder="Your location"
                            background="gray.100"
                            name="location"
                            value={inputs.location}
                            onChange={(e) => handleInputChange("location", e.target.value)}
                        />
                    </Flex>

                    {/* How can we help you fields */}
                    <Field.Root>
                        <Field.Label>How can we help you?</Field.Label>
                        <NativeSelect.Root>
                            <NativeSelect.Field
                                bg={"gray.100"}
                                borderColor={"gray.400"}
                                placeholder="Select an option"
                                color={"black"}
                                value={inputs.categoryId}
                                name="categoryId"
                                onChange={(e) =>
                                    handleInputChange("categoryId", e.currentTarget.value)
                                }
                            >
                                {categories.map((cat) => {
                                    return (
                                        <option key={cat.id} value={cat.id}>
                                            {cat.name}
                                        </option>
                                    );
                                })}
                            </NativeSelect.Field>
                            <NativeSelect.Indicator />
                        </NativeSelect.Root>
                    </Field.Root>

                    {/* Message fields */}
                    <Field.Root>
                        <Field.Label>Message</Field.Label>
                        <Textarea
                            placeholder="Tell us more about your needs and how we can help you..."
                            color={"black"}
                            bg={"gray.100"}
                            borderColor={"gray.400"}
                            value={inputs.message}
                            name="message"
                            onChange={(e) => handleInputChange("message", e.target.value)}
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

export default ContactForm;

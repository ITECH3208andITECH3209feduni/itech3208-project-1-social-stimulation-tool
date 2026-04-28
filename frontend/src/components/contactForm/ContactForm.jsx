import {
    Box,
    Button,
    Checkbox,
    Field,
    Flex,
    Heading,
    NativeSelect,
    Text,
    Textarea,
} from "@chakra-ui/react";
import React from "react";
import NormalField from "../common/fields/NormalField";

function ContactForm() {
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
                        <NormalField fieldLabel="First name" inputPlaceholder="Your first name" />
                        <NormalField fieldLabel="Last name" inputPlaceholder="Your last name" />
                    </Flex>

                    {/* Email and phone number fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            fieldLabel="Phone number"
                            inputPlaceholder="Your phone number"
                        />
                        <NormalField
                            fieldLabel="Email"
                            inputPlaceholder="Your email"
                            type="email"
                        />
                    </Flex>

                    {/* Location and country fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            fieldLabel="Location"
                            inputPlaceholder="Your location"
                            background="gray.100"
                        />
                        <Field.Root>
                            <Field.Label>Country </Field.Label>
                            <NativeSelect.Root>
                                <NativeSelect.Field
                                    bg={"gray.100"}
                                    borderColor={"gray.400"}
                                    placeholder="Select your country"
                                >
                                    <option value="react">React</option>
                                    <option value="react">React</option>
                                    <option value="react">React</option>
                                    <option value="react">React</option>
                                </NativeSelect.Field>
                                <NativeSelect.Indicator />
                            </NativeSelect.Root>
                        </Field.Root>
                    </Flex>

                    {/* How can we help you fields */}
                    <Field.Root>
                        <Field.Label>How can we help you?</Field.Label>
                        <NativeSelect.Root>
                            <NativeSelect.Field
                                bg={"gray.100"}
                                borderColor={"gray.400"}
                                placeholder="Select an option"
                            >
                                <option value="react">React</option>
                                <option value="react">React</option>
                                <option value="react">React</option>
                                <option value="react">React</option>
                            </NativeSelect.Field>
                            <NativeSelect.Indicator />
                        </NativeSelect.Root>
                    </Field.Root>

                    {/* Message fields */}
                    <Field.Root>
                        <Field.Label>Message</Field.Label>
                        <Textarea bg={"gray.100"} borderColor={"gray.400"}>
                            Tell us more about your needs and how we can help you..."
                        </Textarea>
                    </Field.Root>

                    {/* Agree to terms and conitions fields */}
                    <Checkbox.Root variant={"solid"} color={"brand.500"}>
                        <Checkbox.HiddenInput color="brand.500" />
                        <Checkbox.Control />
                        <Checkbox.Label color={"brand.500"}>
                            I agree to recieve terms and conditions from Federation University
                        </Checkbox.Label>
                    </Checkbox.Root>

                    {/* Submit fields */}
                    <Box justifyContent={"center"}>
                        <Button w={"80%"} bg={"skyblue.500"}>
                            <Text>Let's evaluate your print experience</Text>
                        </Button>
                    </Box>
                </Box>
            </Box>
        </Flex>
    );
}

export default ContactForm;

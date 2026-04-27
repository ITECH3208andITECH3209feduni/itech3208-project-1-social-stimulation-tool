import {
    Box,
    Button,
    Checkbox,
    Field,
    Flex,
    Heading,
    HStack,
    Input,
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
            h={"100%"}
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
                    h={"100vh"}
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
                    {/* Row 1 */}
                    <Flex gap={"4"}>
                        <NormalField fieldLabel="First name" inputPlaceholder="Your first name" inputColor={"gray.300"} />
                        <NormalField fieldLabel="Last name" inputPlaceholder="Your last name" inputColor={"gray.300"} />
                    </Flex>
                    {/* Row 2 */}
                    <Flex gap={"4"}>
                        <NormalField fieldLabel="First name" inputPlaceholder="Your first name" inputColor={"gray.300"} />
                        <NormalField fieldLabel="Last name" inputPlaceholder="Your last name" inputColor={"gray.300"} />
                    </Flex>

                    {/* Row 3 */}
                    <Flex gap={"4"}>
                        <NormalField fieldLabel=" name" inputPlaceholder="Your first name" inputColor={"gray.300"} />
                        <Field.Root>
                            <Field.Label >Country *</Field.Label>
                            <NativeSelect.Root>
                                <NativeSelect.Field bg={"gray.300"} placeholder="Select your country">
                                    <option value="react">React</option>
                                    <option value="react">React</option>
                                    <option value="react">React</option>
                                    <option value="react">React</option>
                                </NativeSelect.Field>
                            </NativeSelect.Root>
                        </Field.Root>
                    </Flex>
                    <Field.Root>
                        <Field.Label >How can we help you?</Field.Label>
                        <NativeSelect.Root>
                            <NativeSelect.Field bg={"gray.300"} placeholder="Select an option">
                                <option value="react">React</option>
                                <option value="react">React</option>
                                <option value="react">React</option>
                                <option value="react">React</option>
                            </NativeSelect.Field>
                        </NativeSelect.Root>
                    </Field.Root>
                    <Field.Root>
                        <Field.Label w={"100%"} h={"auto"}>
                            <Textarea bg={"gray.300"}>
                                Tell us more about your needs and how we can help you..."
                            </Textarea>
                        </Field.Label>
                    </Field.Root>
                    <Checkbox.Root variant={"solid"} color={"brand.500"} >
                        <Checkbox.HiddenInput color="brand.500" />
                        <Checkbox.Control  />
                        <Checkbox.Label color={"brand.500"}>I agree to recieve terms and conditions from Federation University</Checkbox.Label>
                    </Checkbox.Root>
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

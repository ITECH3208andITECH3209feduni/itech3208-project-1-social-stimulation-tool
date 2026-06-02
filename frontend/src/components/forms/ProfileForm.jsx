import { Box, Button, Flex, Heading, Text, FileUpload, Image, VStack } from "@chakra-ui/react";
import { useState } from "react";
import { FaUser } from "react-icons/fa6";
import NormalField from "../common/fields/NormalField";
import useAvatarUpload from "@/hooks/custom-hooks/useAvatarUpload";
import userFormRequest from "@/utils/buildUserFormRequest";
import loggerUtil from "@/utils/logger.utils";
import { toaster } from "../ui/toaster";

function ProfileForm({ onSubmit }) {
    const { avatarUpload } = useAvatarUpload();
    const [inputs, setInputs] = useState({});
    const [preview, setPreview] = useState(null);

    // This function handle upload avatar image directly in this component
    const handleFileChange = async ({ acceptedFiles }) => {
        const file = acceptedFiles?.[0];

        if (!file) return;

        // Set image in preview mode for good performance
        const imageUrl = URL.createObjectURL(file);
        setPreview(imageUrl);

        // Create form data for upload file
        const avatarFormData = userFormRequest.buildAvatarFormData({ avatar: file });

        // Upload avatar
        await avatarUpload(avatarFormData, {
            onSuccess: (data, _) => {
                // Update real url when upload successfully
                const avatarUrl = data.avatar.url;
                setPreview(avatarUrl);
            },
            onError: (msg) => {
                toaster.create({
                    description: msg,
                    type: "error",
                });
                // Log the error in console for checking any upload file error
                loggerUtil.error(msg);
            },
        });
    };

    const handleInputChange = (key, value) => {
        setInputs((prev) => ({ ...prev, [key]: value }));
    };

    const handleSubmit = () => onSubmit(inputs);

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
                    Update Your Profile
                </Heading>
                <Text>
                    Keep your Scenario Aid account information up to date to enjoy a more
                    personalized learning and support experience.
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
                    <Flex justify={"center"}>
                        <VStack>
                            <FileUpload.Root
                                w="120px"
                                h="120px"
                                position={"relative"}
                                borderWidth={2}
                                rounded="full"
                                maxFiles={1}
                                onFileChange={handleFileChange}
                                display="flex"
                                justifyContent="center"
                                alignItems="center"
                                flexDirection="column"
                            >
                                <FileUpload.HiddenInput />
                                <FileUpload.Trigger>
                                    <Box
                                        w="120px"
                                        h="120px"
                                        display="flex"
                                        justifyContent="center"
                                        alignItems="center"
                                        flexDirection="column"
                                        overflow="hidden"
                                        cursor="pointer"
                                        rounded="full"
                                    >
                                        {preview ? (
                                            <Image
                                                src={preview}
                                                w="100%"
                                                h="100%"
                                                objectFit="cover"
                                            />
                                        ) : (
                                            <FaUser size={50} />
                                        )}
                                    </Box>
                                </FileUpload.Trigger>
                            </FileUpload.Root>
                            <Text cursor="pointer">Upload your avatar</Text>
                        </VStack>
                    </Flex>

                    {/* First and last name fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            name="firstName"
                            fieldLabel="First name"
                            inputPlaceholder="Your first name"
                            onChange={(e) => handleInputChange("firstName", e.target.value)}
                        />
                        <NormalField
                            name="lastName"
                            fieldLabel="Last name"
                            inputPlaceholder="Your last name"
                            onChange={(e) => handleInputChange("lastName", e.target.value)}
                        />
                    </Flex>

                    {/* Email and location fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            name="email"
                            fieldLabel="Email"
                            inputPlaceholder="Your email"
                            type="email"
                            onChange={(e) => handleInputChange("email", e.target.value)}
                        />
                        <NormalField
                            name="location"
                            fieldLabel="Location"
                            inputPlaceholder="Your location"
                            background="gray.100"
                            onChange={(e) => handleInputChange("location", e.target.value)}
                        />
                    </Flex>

                    {/* Submit fields */}
                    <Box justifyContent={"center"}>
                        <Button w={"40%"} bg={"skyblue.500"} onClick={handleSubmit}>
                            <Text>Update</Text>
                        </Button>
                    </Box>
                </Box>
            </Box>
        </Flex>
    );
}

export default ProfileForm;

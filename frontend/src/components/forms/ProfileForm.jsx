import { Box, Button, Flex, Heading, Text, Image, VStack } from "@chakra-ui/react";
import { useState } from "react";
import NormalField from "../common/fields/NormalField";
import AvatarUploadField from "../common/fields/AvatarUploadField";
import useAvatarUpload from "@/hooks/custom-hooks/useAvatarUpload";

function ProfileForm({ user, onSubmit }) {
    const [inputs, setInputs] = useState({
        firstName: user?.firstName || "",
        lastName: user?.lastName || "",
        email: user?.email || "",
        location: user?.location || "",
    });
    const [preview, setPreview] = useState(user?.avatar?.url);
    const { uploadAvatar } = useAvatarUpload(setPreview);

    // This function handle upload avatar image directly in this component
    const handleFileChange = async ({ acceptedFiles }) => {
        const file = acceptedFiles?.[0];
        if (file) {
            uploadAvatar(file);
        }
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
                    <AvatarUploadField
                        preview={preview}
                        isUploading={user?.isUploading}
                        onFileChange={handleFileChange}
                    />

                    {/* First and last name fields */}
                    <Flex gap={"4"}>
                        <NormalField
                            name="firstName"
                            fieldLabel="First name"
                            inputPlaceholder="Your first name"
                            value={inputs.firstName}
                            onChange={(e) => handleInputChange("firstName", e.target.value)}
                        />
                        <NormalField
                            name="lastName"
                            fieldLabel="Last name"
                            inputPlaceholder="Your last name"
                            value={inputs.lastName}
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
                            value={inputs.email}
                            onChange={(e) => handleInputChange("email", e.target.value)}
                        />
                        <NormalField
                            name="location"
                            fieldLabel="Location"
                            inputPlaceholder="Your location"
                            background="gray.100"
                            value={inputs.location}
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

import { Flex, VStack, FileUpload, Box, Image, Text, Spinner } from "@chakra-ui/react";
import { FaUser } from "react-icons/fa6";
import PropTypes from "prop-types";

function AvatarUploadField({ preview, isUploading, onFileChange }) {
    return (
        <Flex justify={"center"}>
            <VStack>
                <FileUpload.Root
                    w="120px"
                    h="120px"
                    position={"relative"}
                    borderWidth={2}
                    rounded="full"
                    maxFiles={1}
                    onFileChange={onFileChange}
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
                                <Image src={preview} w="100%" h="100%" objectFit="cover" />
                            ) : (
                                <FaUser size={50} />
                            )}
                            {isUploading && (
                                <Box
                                    position="absolute"
                                    top={0}
                                    left={0}
                                    right={0}
                                    bottom={0}
                                    bg="blackAlpha.500"
                                    display="flex"
                                    justifyContent="center"
                                    alignItems="center"
                                    rounded="full"
                                    zIndex={10}
                                >
                                    <Spinner size="xl" color="white" thickness="4px" />
                                </Box>
                            )}
                        </Box>
                    </FileUpload.Trigger>
                </FileUpload.Root>
                <Text cursor="pointer">Upload your avatar</Text>
            </VStack>
        </Flex>
    );
}

AvatarUploadField.propTypes = {
    preview: PropTypes.string,
    isUploading: PropTypes.bool,
    onFileChange: PropTypes.func.isRequired,
};

export default AvatarUploadField;

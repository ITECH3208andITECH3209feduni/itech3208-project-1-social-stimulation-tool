import { Field, FileUpload, VStack, Box, Text, Image, IconButton, HStack } from "@chakra-ui/react";
import { BiUpload, BiX, BiFile } from "react-icons/bi";
import React, { useState, useEffect } from "react";
import PropTypes from "prop-types";

function FileUploadField({
    name,
    fieldLabel,
    accept,
    maxFileSize,
    maxFiles = 1,
    helpText,
    onFileChange,
    onFileRemove,
    initialPreviewUrl,
    errorText,
    value,
}) {
    const [preview, setPreview] = useState(initialPreviewUrl || null);
    const [selectedFile, setSelectedFile] = useState(null);

    useEffect(() => {
        if (initialPreviewUrl) {
            setPreview(initialPreviewUrl);
        }
    }, [initialPreviewUrl]);

    // Sync internal state with external value prop
    useEffect(() => {
        if (value === null || value === undefined) {
            setSelectedFile(null);
            setPreview(initialPreviewUrl || null);
        }
    }, [value, initialPreviewUrl]);

    // Clean up object URL to avoid memory leaks
    useEffect(() => {
        return () => {
            if (typeof preview === "string" && preview.startsWith("blob:")) {
                URL.revokeObjectURL(preview);
            }
        };
    }, [preview]);

    const handleFileChange = (e) => {
        const file = e.acceptedFiles?.[0];
        if (file) {
            setSelectedFile(file);
            // Check if file is image or video for media preview
            if (file.type.startsWith("image/") || file.type.startsWith("video/")) {
                const objectUrl = URL.createObjectURL(file);
                setPreview(objectUrl);
            } else {
                // Use generic file icon preview
                setPreview("file_icon");
            }
            onFileChange?.(file);
        }
    };

    const handleClear = (e) => {
        e.stopPropagation();
        e.preventDefault();
        setSelectedFile(null);
        setPreview(null);
        onFileRemove?.();
    };

    return (
        <Field.Root invalid={!!errorText}>
            {fieldLabel && <Field.Label>{fieldLabel}</Field.Label>}
            <FileUpload.Root
                name={name}
                accept={accept}
                maxFileSize={maxFileSize}
                maxFiles={maxFiles}
                onFileChange={handleFileChange}
                alignItems="stretch"
            >
                {!preview && (
                    <>
                        <FileUpload.HiddenInput />
                        <FileUpload.Trigger w="100%">
                            <Box
                                p={8}
                                borderWidth="2px"
                                borderStyle="dashed"
                                borderColor="whiteAlpha.500"
                                rounded="lg"
                                cursor="pointer"
                                bg="dark.900"
                                _hover={{ bg: "whiteAlpha.50", borderColor: "brand.500" }}
                                transition="all 0.2s"
                                w="100%"
                            >
                                <VStack gap={3} align="center" color="whiteAlpha.700">
                                    <BiUpload size={40} />
                                    <Text fontWeight="bold" fontSize="lg">
                                        Click to upload
                                    </Text>
                                    <Text fontSize="sm" color="whiteAlpha.500">
                                        {helpText || "Drag and drop your file here"}
                                    </Text>
                                </VStack>
                            </Box>
                        </FileUpload.Trigger>
                    </>
                )}

                {preview && (
                    <Box
                        borderWidth="1px"
                        borderColor="whiteAlpha.500"
                        rounded="lg"
                        p={2}
                        bg="dark.900"
                        position="relative"
                    >
                        {preview === "file_icon" ? (
                            <HStack p={4} gap={4}>
                                <BiFile size={40} color="#B81F24" />
                                <VStack align="start" gap={0} flex={1} overflow="hidden">
                                    <Text fontWeight="medium" noOfLines={1} color="whiteAlpha.900">
                                        {selectedFile?.name || "Uploaded File"}
                                    </Text>
                                    {selectedFile && (
                                        <Text fontSize="sm" color="whiteAlpha.500">
                                            {(selectedFile.size / 1024 / 1024).toFixed(2)} MB
                                        </Text>
                                    )}
                                </VStack>
                            </HStack>
                        ) : (
                            <Box
                                w="100%"
                                h="250px"
                                bg="dark.800"
                                rounded="md"
                                overflow="hidden"
                                display="flex"
                                justifyContent="center"
                                alignItems="center"
                            >
                                {selectedFile?.type?.startsWith("video/") ||
                                (preview !== "file_icon" && preview.match(/\.(mp4|webm|ogg)$/i)) ? (
                                    <video
                                        src={preview}
                                        controls
                                        style={{ maxWidth: "100%", maxHeight: "100%" }}
                                    />
                                ) : (
                                    <Image
                                        src={preview}
                                        alt="Preview"
                                        maxW="100%"
                                        maxH="100%"
                                        objectFit="contain"
                                    />
                                )}
                            </Box>
                        )}
                        <IconButton
                            aria-label="Remove file"
                            size="sm"
                            bg="red.500"
                            _hover={{ bg: "red.600" }}
                            position="absolute"
                            top={4}
                            right={4}
                            onClick={handleClear}
                            rounded="full"
                        >
                            <BiX size={20} color="white" />
                        </IconButton>
                    </Box>
                )}
            </FileUpload.Root>
            {errorText && <Field.ErrorText>{errorText}</Field.ErrorText>}
        </Field.Root>
    );
}

FileUploadField.propTypes = {
    name: PropTypes.string,
    fieldLabel: PropTypes.string,
    accept: PropTypes.oneOfType([PropTypes.string, PropTypes.array]),
    maxFileSize: PropTypes.number,
    maxFiles: PropTypes.number,
    helpText: PropTypes.string,
    onFileChange: PropTypes.func,
    onFileRemove: PropTypes.func,
    initialPreviewUrl: PropTypes.string,
};

export default FileUploadField;

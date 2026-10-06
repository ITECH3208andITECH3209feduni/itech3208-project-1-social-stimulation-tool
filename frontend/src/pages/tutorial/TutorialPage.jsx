import React, { useState } from "react";
import { Container, Heading, Box, SimpleGrid, Text, VStack, Skeleton, Center } from "@chakra-ui/react";
import HighlightText from "../terms&conditions/HighlightText";

const GrantMeredithInfoUrl =
    "https://www.federation.edu.au/research/find-an-expert/grant-meredith/";
const videoUrl = "https://www.youtube.com/embed/ScMzIvxBSi4";

const VideoSkeleton = () => (
    <Box position="relative" w="full" style={{ aspectRatio: "16/9" }}>
        <Skeleton w="full" h="full" position="absolute" top={0} left={0} />
        <Center position="absolute" top={0} left={0} w="full" h="full">
            <Box
                w="68px"
                h="48px"
                bg="blackAlpha.500"
                borderRadius="xl"
                display="flex"
                alignItems="center"
                justifyContent="center"
            >
                <Skeleton
                    w="0"
                    h="0"
                    borderTop="10px solid transparent"
                    borderBottom="10px solid transparent"
                    borderLeft="16px solid white"
                    ml={1}
                />
            </Box>
        </Center>
    </Box>
);

const VideoFallback = () => (
    <Box
        w="full"
        style={{ aspectRatio: "16/9" }}
        bg="gray.100"
        display="flex"
        flexDirection="column"
        alignItems="center"
        justifyContent="center"
        p={6}
        textAlign="center"
    >
        <Heading as="h4" size="md" color="gray.500" mb={3}>
            Video Unavailable
        </Heading>
        <Text color="gray.500">
            The tutorial video will be updated soon. Please check back later.
        </Text>
    </Box>
);

function TutorialPage() {
    const [hasError, setHasError] = useState(false);
    const [isIframeLoading, setIsIframeLoading] = useState(true);

    return (
        <Container mt={"100px"} spaceY={"50px"} maxW="container.xl" mb={"100px"}>
            <Heading
                color="brand.500"
                fontFamily="Sora"
                fontWeight={"bold"}
                fontSize={48}
                textAlign="center"
            >
                Getting Started with Scenari-Aid
            </Heading>

            <SimpleGrid columns={{ base: 1, lg: 2 }} gap={6} alignItems="center">
                <Box w="full" borderRadius="xl" overflow="hidden" boxShadow="2xl" bg="gray.50">
                    {videoUrl && !hasError ? (
                        <>
                            {isIframeLoading && <VideoSkeleton />}
                            <iframe
                                width="100%"
                                style={{
                                    aspectRatio: "16/9",
                                    display: isIframeLoading ? "none" : "block",
                                }}
                                src={videoUrl}
                                title="Scenari-Aid Tutorial"
                                frameBorder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowFullScreen
                                onLoad={() => setIsIframeLoading(false)}
                                onError={() => setHasError(true)}
                            ></iframe>
                        </>
                    ) : (
                        <VideoFallback />
                    )}
                </Box>

                <VStack
                    alignSelf="start"
                    align="flex-start"
                    gap={5}
                    textAlign={"left"}
                    px={2}
                    bg={"gray.50"}
                    h={"full"}
                    rounded={"md"}
                >
                    <Heading as="h3" size="lg" color="brand.500" fontFamily="Sora">
                        About This Tutorial
                    </Heading>
                    <HighlightText
                        fontSize="lg"
                        color="brand.500"
                        lineHeight="tall"
                        text="This tutorial video was created by Grant Meredith. It is designed to guide users through the various features of the Scenari-Aid website and demonstrate how to effectively use the platform to maximize your experience."
                        highlights={[{ text: "Grant Meredith", url: GrantMeredithInfoUrl }]}
                    />
                    <Text fontSize="lg" color="gray.600" lineHeight="tall">
                        Watch the video to learn step-by-step instructions on navigating our
                        scenarios, using our tools, and getting the most out of Scenari-Aid.
                    </Text>
                </VStack>
            </SimpleGrid>
        </Container>
    );
}

export default TutorialPage;

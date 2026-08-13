import { Card, VStack, HStack, Spacer, Skeleton as ChakraSkeleton, SkeletonCircle as ChakraSkeletonCircle, Box, Flex } from "@chakra-ui/react";

const Skeleton = (props) => <ChakraSkeleton startColor="whiteAlpha.200" endColor="whiteAlpha.400" bg="whiteAlpha.200" {...props} />;
const SkeletonCircle = (props) => <ChakraSkeletonCircle startColor="whiteAlpha.200" endColor="whiteAlpha.400" bg="whiteAlpha.200" {...props} />;

function UserSkeletonCard() {
    return (
        <Card.Root
            bg="dark.800"
            color="whiteAlpha.900"
            rounded="xl"
            overflow="hidden"
            borderWidth="1px"
            borderColor="whiteAlpha.200"
            boxShadow="0 8px 32px rgba(0, 0, 0, 0.3)"
            position="relative"
            w="full"
        >
            {/* Glassmorphism subtle glow effect */}
            <Box
                position="absolute"
                top="-50px"
                right="-50px"
                w="150px"
                h="150px"
                bg="brand.500"
                filter="blur(80px)"
                opacity={0.3}
                borderRadius="full"
                zIndex={0}
            />

            <Box p={6} position="relative" zIndex={1}>
                {/* ID & Status Row */}
                <Flex justify="space-between" align="center" mb={6}>
                    <HStack>
                        <SkeletonCircle size="4" />
                        <Skeleton height="16px" width="80px" />
                    </HStack>
                    <Skeleton height="24px" width="70px" borderRadius="full" />
                </Flex>

                {/* Avatar & Name Section */}
                <Flex gap={5} align="center" mb={6}>
                    <SkeletonCircle size="20" /> 
                    <VStack align="start" gap={3} flex={1}>
                        <Skeleton height="24px" width="80%" />
                        <Skeleton height="16px" width="50%" />
                    </VStack>
                </Flex>

                <Box borderBottomWidth="1px" borderColor="whiteAlpha.200" mb={5} w="full" />

                {/* Info Section */}
                <VStack align="start" gap={4} mb={6}>
                    <HStack w="full" gap={3}>
                        <SkeletonCircle size="4" />
                        <Skeleton height="16px" width="40%" />
                    </HStack>
                    <HStack w="full" gap={3}>
                        <SkeletonCircle size="4" />
                        <Skeleton height="16px" width="70%" />
                    </HStack>
                    <HStack w="full" gap={3}>
                        <SkeletonCircle size="4" />
                        <Skeleton height="16px" width="50%" />
                    </HStack>
                    <HStack w="full" gap={3}>
                        <SkeletonCircle size="4" />
                        <Skeleton height="16px" width="80%" />
                    </HStack>
                    <HStack w="full" gap={3}>
                        <SkeletonCircle size="4" />
                        <Skeleton height="16px" width="60%" />
                    </HStack>
                </VStack>
            </Box>

            {/* Actions Footer */}
            <Flex
                bg="whiteAlpha.50"
                borderTopWidth="1px"
                borderColor="whiteAlpha.100"
                p={4}
                gap={3}
            >
                <Skeleton height="40px" flex={1} borderRadius="md" />
                <Skeleton height="40px" flex={1} borderRadius="md" />
            </Flex>
        </Card.Root>
    );
}

export default UserSkeletonCard;

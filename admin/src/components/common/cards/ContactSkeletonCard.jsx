import { Card, VStack, HStack, Spacer, Skeleton as ChakraSkeleton, SkeletonCircle as ChakraSkeletonCircle, Box } from "@chakra-ui/react";

const Skeleton = (props) => <ChakraSkeleton startColor="whiteAlpha.200" endColor="whiteAlpha.400" bg="whiteAlpha.200" {...props} />;
const SkeletonCircle = (props) => <ChakraSkeletonCircle startColor="whiteAlpha.200" endColor="whiteAlpha.400" bg="whiteAlpha.200" {...props} />;

function ContactSkeletonCard() {
    return (
        <Card.Root bg="dark.800" color="whiteAlpha.900" rounded="md" h={"350px"}>
            <Card.Header>
                <HStack gap={3}>
                    <SkeletonCircle size="10" />
                    <Skeleton height="20px" width="120px" />
                    <Spacer />
                    <Skeleton height="24px" width="60px" borderRadius="md" />
                </HStack>
            </Card.Header>

            <Card.Body>
                <VStack align="start" gap={4} w="full">
                    {/* Rows matching icon + text */}
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
                        <Skeleton height="16px" width="40%" />
                    </HStack>
                    
                    {/* Description simulation */}
                    <VStack w="full" align="start" gap={2} mt={2}>
                        <Skeleton height="14px" width="100%" />
                        <Skeleton height="14px" width="100%" />
                        <Skeleton height="14px" width="60%" />
                    </VStack>
                </VStack>
            </Card.Body>

            <Card.Footer>
                <Skeleton height="40px" width="full" borderRadius="md" />
            </Card.Footer>
        </Card.Root>
    );
}

export default ContactSkeletonCard;

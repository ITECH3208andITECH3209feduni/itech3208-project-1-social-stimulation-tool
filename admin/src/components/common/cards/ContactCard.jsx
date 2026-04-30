import StatusBadge from "@/components/badges/StatusBadge";
import { Card, Text, VStack, HStack, Icon, Badge, Spacer, Button } from "@chakra-ui/react";
import { FaEnvelope, FaPhone, FaMapMarkerAlt, FaCircle } from "react-icons/fa";

function ContactCard({ contact }) {
    return (
        <Card.Root bg="dark.800" color="whiteAlpha.900" rounded="md" h={"350px"}>
            {/* Header */}
            <Card.Header>
                <HStack>
                    <Card.Title fontSize="xl">{contact.name}</Card.Title>
                    <Spacer />
                    <StatusBadge status={contact.status} />
                </HStack>
            </Card.Header>

            {/* Body */}
            <Card.Body>
                <VStack align="start" gap={3}>
                    {/* Email */}
                    <HStack fontSize="sm" color="gray.300">
                        <Icon as={FaEnvelope} />
                        <Text>{contact.email}</Text>
                    </HStack>

                    {/* Phone (optional) */}
                    {contact.phone && (
                        <HStack fontSize="sm" color="gray.300">
                            <Icon as={FaPhone} />
                            <Text>{contact.phone}</Text>
                        </HStack>
                    )}

                    {/* Address (optional) */}
                    {contact.address && (
                        <HStack fontSize="sm" color="gray.300">
                            <Icon as={FaMapMarkerAlt} />
                            <Text>{contact.address}</Text>
                        </HStack>
                    )}

                    {/* Description */}
                    <Text fontSize="sm" color="gray.400">
                        {contact.description}
                    </Text>
                </VStack>
            </Card.Body>

            {/* Footer */}
            <Card.Footer>
                <HStack w="100%" justify="end" gap={2}>
                    <Button flex={1} bg={"#48BB78"}>
                        Reply
                        <FaPhone />
                    </Button>
                    <Button flex={1} bg={"#0BC5EA"}>
                        Reply <FaEnvelope />
                    </Button>
                    <Button flex={1} bg={"#0BC5EA"}>
                        Update Status
                    </Button>
                </HStack>
            </Card.Footer>
        </Card.Root>
    );
}

export default ContactCard;

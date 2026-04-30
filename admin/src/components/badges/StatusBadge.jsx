import { Badge } from "@chakra-ui/react";
import { FaCircle } from "react-icons/fa";

export const STATUS = {
    PENDING: {
        value: "pending",
        label: "Pending",
        color: "#ECC94B", // yellow.500
    },
    IN_PROGRESS: {
        value: "in_progress",
        label: "In Progress",
        color: "#4299E1", // blue.500
    },
    RESOLVED: {
        value: "resolved",
        label: "Resolved",
        color: "#48BB78", // green.500
    },
    REJECTED: {
        value: "rejected",
        label: "Rejected",
        color: "#F56565", // red.500
    },
    CLOSED: {
        value: "closed",
        label: "Closed",
        color: "#718096", // gray.500
    },
};

function StatusBadge({ status = STATUS.PENDING.value }) {
    // Find status object by value
    const statusObj = Object.values(STATUS).find((s) => s.value === status) || STATUS.PENDING;

    return (
        <Badge px={2} color={statusObj.color} bg="gray.800" fontSize={12}>
            <FaCircle color={statusObj.color} size={8} />
            {statusObj.label}
        </Badge>
    );
}

export default StatusBadge;

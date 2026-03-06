"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Badge } from "@/components/ui/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { 
  Search, 
  CheckCircle, 
  XCircle, 
  Eye,
  Phone,
  Mail,
  Calendar,
  Clock,
  Users,
  MapPin,
} from "lucide-react"

// Mock data
const allReservations = [
  {
    id: "RES-2026-0320",
    guest: "Sarah Johnson",
    email: "sarah@example.com",
    phone: "(555) 111-2222",
    partySize: 4,
    zone: "Main Dining Room",
    date: "March 6, 2026",
    time: "7:30 PM",
    status: "pending",
    specialRequests: "Anniversary dinner, window table preferred",
    createdAt: "March 4, 2026",
  },
  {
    id: "RES-2026-0319",
    guest: "Michael Brown",
    email: "michael@example.com",
    phone: "(555) 333-4444",
    partySize: 2,
    zone: "The Bar",
    date: "March 6, 2026",
    time: "6:00 PM",
    status: "confirmed",
    specialRequests: "",
    createdAt: "March 3, 2026",
  },
  {
    id: "RES-2026-0318",
    guest: "Emily Davis",
    email: "emily@example.com",
    phone: "(555) 555-6666",
    partySize: 6,
    zone: "The Patio",
    date: "March 6, 2026",
    time: "8:00 PM",
    status: "pending",
    specialRequests: "Birthday celebration, need high chair",
    createdAt: "March 2, 2026",
  },
  {
    id: "RES-2026-0317",
    guest: "James Wilson",
    email: "james@example.com",
    phone: "(555) 777-8888",
    partySize: 2,
    zone: "Main Dining Room",
    date: "March 6, 2026",
    time: "7:00 PM",
    status: "confirmed",
    specialRequests: "Gluten-free menu needed",
    createdAt: "March 1, 2026",
  },
  {
    id: "RES-2026-0316",
    guest: "Lisa Anderson",
    email: "lisa@example.com",
    phone: "(555) 999-0000",
    partySize: 8,
    zone: "Main Dining Room",
    date: "March 5, 2026",
    time: "7:30 PM",
    status: "cancelled",
    specialRequests: "Business dinner",
    createdAt: "February 28, 2026",
  },
]

type Reservation = typeof allReservations[0]

export default function AdminReservationsPage() {
  const [searchQuery, setSearchQuery] = useState("")
  const [dateFilter, setDateFilter] = useState("today")
  const [selectedReservation, setSelectedReservation] = useState<Reservation | null>(null)
  const [detailsDialogOpen, setDetailsDialogOpen] = useState(false)
  const [actionDialogOpen, setActionDialogOpen] = useState(false)
  const [actionType, setActionType] = useState<"approve" | "reject">("approve")

  const pendingReservations = allReservations.filter(r => r.status === "pending")
  const confirmedReservations = allReservations.filter(r => r.status === "confirmed")
  const cancelledReservations = allReservations.filter(r => r.status === "cancelled")

  const handleViewDetails = (reservation: Reservation) => {
    setSelectedReservation(reservation)
    setDetailsDialogOpen(true)
  }

  const handleAction = (reservation: Reservation, action: "approve" | "reject") => {
    setSelectedReservation(reservation)
    setActionType(action)
    setActionDialogOpen(true)
  }

  const confirmAction = () => {
    // In production, this would call an API
    setActionDialogOpen(false)
    setSelectedReservation(null)
  }

  const ReservationRow = ({ reservation }: { reservation: Reservation }) => (
    <div className="flex flex-col lg:flex-row lg:items-center justify-between p-4 bg-card rounded-sm border border-border gap-4">
      <div className="flex-1 space-y-1">
        <div className="flex items-center gap-2 flex-wrap">
          <span className="font-medium text-foreground">{reservation.guest}</span>
          <Badge
            variant={
              reservation.status === "confirmed"
                ? "default"
                : reservation.status === "pending"
                ? "secondary"
                : "destructive"
            }
            className={
              reservation.status === "confirmed"
                ? "bg-green-100 text-green-700 hover:bg-green-100"
                : reservation.status === "pending"
                ? "bg-yellow-100 text-yellow-700 hover:bg-yellow-100"
                : ""
            }
          >
            {reservation.status}
          </Badge>
        </div>
        <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
          <span className="flex items-center gap-1">
            <MapPin className="h-3 w-3" />
            {reservation.zone}
          </span>
          <span className="flex items-center gap-1">
            <Calendar className="h-3 w-3" />
            {reservation.date}
          </span>
          <span className="flex items-center gap-1">
            <Clock className="h-3 w-3" />
            {reservation.time}
          </span>
          <span className="flex items-center gap-1">
            <Users className="h-3 w-3" />
            {reservation.partySize} guests
          </span>
        </div>
        <p className="text-xs text-muted-foreground">#{reservation.id}</p>
      </div>
      
      <div className="flex items-center gap-2">
        <Button 
          size="sm" 
          variant="outline"
          onClick={() => handleViewDetails(reservation)}
        >
          <Eye className="h-4 w-4 mr-1" />
          Details
        </Button>
        {reservation.status === "pending" && (
          <>
            <Button 
              size="sm" 
              variant="outline" 
              className="text-green-600 hover:text-green-600 hover:bg-green-50"
              onClick={() => handleAction(reservation, "approve")}
            >
              <CheckCircle className="h-4 w-4 mr-1" />
              Approve
            </Button>
            <Button 
              size="sm" 
              variant="outline" 
              className="text-destructive hover:text-destructive hover:bg-destructive/10"
              onClick={() => handleAction(reservation, "reject")}
            >
              <XCircle className="h-4 w-4 mr-1" />
              Reject
            </Button>
          </>
        )}
      </div>
    </div>
  )

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">Reservations</h1>
          <p className="text-muted-foreground mt-1">
            Manage and approve guest reservations
          </p>
        </div>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="p-4">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search by guest name or confirmation #"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-9 bg-background"
              />
            </div>
            <Select value={dateFilter} onValueChange={setDateFilter}>
              <SelectTrigger className="w-full md:w-48 bg-background">
                <SelectValue placeholder="Filter by date" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="today">Today</SelectItem>
                <SelectItem value="tomorrow">Tomorrow</SelectItem>
                <SelectItem value="week">This Week</SelectItem>
                <SelectItem value="month">This Month</SelectItem>
                <SelectItem value="all">All Time</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Tabs */}
      <Tabs defaultValue="pending" className="space-y-6">
        <TabsList className="grid w-full max-w-lg grid-cols-3">
          <TabsTrigger value="pending">
            Pending ({pendingReservations.length})
          </TabsTrigger>
          <TabsTrigger value="confirmed">
            Confirmed ({confirmedReservations.length})
          </TabsTrigger>
          <TabsTrigger value="cancelled">
            Cancelled ({cancelledReservations.length})
          </TabsTrigger>
        </TabsList>

        <TabsContent value="pending" className="space-y-4">
          {pendingReservations.length > 0 ? (
            pendingReservations.map((reservation) => (
              <ReservationRow key={reservation.id} reservation={reservation} />
            ))
          ) : (
            <Card>
              <CardContent className="py-12 text-center">
                <CheckCircle className="h-12 w-12 text-green-500 mx-auto mb-4" />
                <p className="text-muted-foreground">All caught up! No pending reservations.</p>
              </CardContent>
            </Card>
          )}
        </TabsContent>

        <TabsContent value="confirmed" className="space-y-4">
          {confirmedReservations.length > 0 ? (
            confirmedReservations.map((reservation) => (
              <ReservationRow key={reservation.id} reservation={reservation} />
            ))
          ) : (
            <Card>
              <CardContent className="py-12 text-center">
                <p className="text-muted-foreground">No confirmed reservations.</p>
              </CardContent>
            </Card>
          )}
        </TabsContent>

        <TabsContent value="cancelled" className="space-y-4">
          {cancelledReservations.length > 0 ? (
            cancelledReservations.map((reservation) => (
              <ReservationRow key={reservation.id} reservation={reservation} />
            ))
          ) : (
            <Card>
              <CardContent className="py-12 text-center">
                <p className="text-muted-foreground">No cancelled reservations.</p>
              </CardContent>
            </Card>
          )}
        </TabsContent>
      </Tabs>

      {/* Details Dialog */}
      <Dialog open={detailsDialogOpen} onOpenChange={setDetailsDialogOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Reservation Details</DialogTitle>
          </DialogHeader>
          {selectedReservation && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="font-mono text-sm text-muted-foreground">
                  {selectedReservation.id}
                </span>
                <Badge
                  className={
                    selectedReservation.status === "confirmed"
                      ? "bg-green-100 text-green-700"
                      : selectedReservation.status === "pending"
                      ? "bg-yellow-100 text-yellow-700"
                      : ""
                  }
                >
                  {selectedReservation.status}
                </Badge>
              </div>

              <div className="space-y-3 p-4 bg-muted rounded-sm">
                <div className="flex items-center gap-3">
                  <Users className="h-4 w-4 text-primary" />
                  <div>
                    <p className="font-medium text-foreground">{selectedReservation.guest}</p>
                    <p className="text-sm text-muted-foreground">{selectedReservation.partySize} guests</p>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <Mail className="h-4 w-4 text-primary" />
                  <span className="text-sm">{selectedReservation.email}</span>
                </div>
                <div className="flex items-center gap-3">
                  <Phone className="h-4 w-4 text-primary" />
                  <span className="text-sm">{selectedReservation.phone}</span>
                </div>
                <div className="flex items-center gap-3">
                  <MapPin className="h-4 w-4 text-primary" />
                  <span className="text-sm">{selectedReservation.zone}</span>
                </div>
                <div className="flex items-center gap-3">
                  <Calendar className="h-4 w-4 text-primary" />
                  <span className="text-sm">{selectedReservation.date} at {selectedReservation.time}</span>
                </div>
              </div>

              {selectedReservation.specialRequests && (
                <div className="p-4 bg-accent/50 rounded-sm">
                  <p className="text-sm font-medium text-foreground mb-1">Special Requests</p>
                  <p className="text-sm text-muted-foreground">{selectedReservation.specialRequests}</p>
                </div>
              )}

              <p className="text-xs text-muted-foreground">
                Booked on {selectedReservation.createdAt}
              </p>
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* Action Dialog */}
      <Dialog open={actionDialogOpen} onOpenChange={setActionDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {actionType === "approve" ? "Approve Reservation" : "Reject Reservation"}
            </DialogTitle>
            <DialogDescription>
              {actionType === "approve"
                ? "This will confirm the reservation and notify the guest."
                : "This will cancel the reservation and notify the guest."}
            </DialogDescription>
          </DialogHeader>
          {selectedReservation && (
            <div className="p-4 bg-muted rounded-sm">
              <p className="font-medium text-foreground">{selectedReservation.guest}</p>
              <p className="text-sm text-muted-foreground">
                {selectedReservation.zone} • {selectedReservation.date} at {selectedReservation.time}
              </p>
            </div>
          )}
          <DialogFooter>
            <Button variant="outline" onClick={() => setActionDialogOpen(false)}>
              Cancel
            </Button>
            <Button
              onClick={confirmAction}
              className={
                actionType === "approve"
                  ? "bg-green-600 hover:bg-green-700 text-white"
                  : ""
              }
              variant={actionType === "reject" ? "destructive" : "default"}
            >
              {actionType === "approve" ? "Approve" : "Reject"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}

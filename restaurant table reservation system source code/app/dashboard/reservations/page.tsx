"use client"

import { useState } from "react"
import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Badge } from "@/components/ui/badge"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { CalendarPlus, MapPin, Users, Clock, X } from "lucide-react"

// Mock data
const reservations = {
  pending: [
    {
      id: "RES-2026-0302",
      zone: "The Bar",
      date: "Friday, March 20, 2026",
      time: "6:00 PM",
      partySize: 2,
      status: "pending",
      createdAt: "March 5, 2026",
    },
  ],
  confirmed: [
    {
      id: "RES-2026-0301",
      zone: "Main Dining Room",
      date: "Saturday, March 14, 2026",
      time: "7:30 PM",
      partySize: 4,
      status: "confirmed",
      createdAt: "March 1, 2026",
    },
  ],
  cancelled: [
    {
      id: "RES-2026-0298",
      zone: "The Patio",
      date: "Sunday, February 28, 2026",
      time: "8:00 PM",
      partySize: 6,
      status: "cancelled",
      createdAt: "February 20, 2026",
      cancelledAt: "February 26, 2026",
    },
  ],
}

type Reservation = typeof reservations.pending[0]

export default function ReservationsPage() {
  const [cancelDialogOpen, setCancelDialogOpen] = useState(false)
  const [selectedReservation, setSelectedReservation] = useState<Reservation | null>(null)

  const handleCancelClick = (reservation: Reservation) => {
    setSelectedReservation(reservation)
    setCancelDialogOpen(true)
  }

  const handleConfirmCancel = () => {
    // In production, this would call an API to cancel the reservation
    setCancelDialogOpen(false)
    setSelectedReservation(null)
  }

  const ReservationCard = ({ reservation, showActions = true }: { reservation: Reservation, showActions?: boolean }) => (
    <div className="p-6 bg-card rounded-sm border border-border">
      <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div className="space-y-3">
          <div className="flex items-center gap-2 flex-wrap">
            <h3 className="font-serif text-lg text-foreground">{reservation.zone}</h3>
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

          <div className="space-y-2 text-sm">
            <div className="flex items-center gap-2 text-muted-foreground">
              <Clock className="h-4 w-4" />
              <span>{reservation.date} at {reservation.time}</span>
            </div>
            <div className="flex items-center gap-2 text-muted-foreground">
              <Users className="h-4 w-4" />
              <span>{reservation.partySize} guests</span>
            </div>
            <div className="flex items-center gap-2 text-muted-foreground">
              <MapPin className="h-4 w-4" />
              <span>Eudaimonia Restaurant</span>
            </div>
          </div>

          <p className="text-xs text-muted-foreground">
            Confirmation #{reservation.id}
          </p>
        </div>

        {showActions && reservation.status !== "cancelled" && (
          <div className="flex gap-2 sm:flex-col">
            <Button variant="outline" size="sm" className="flex-1 sm:flex-none">
              Modify
            </Button>
            <Button
              variant="ghost"
              size="sm"
              className="flex-1 sm:flex-none text-destructive hover:text-destructive"
              onClick={() => handleCancelClick(reservation)}
            >
              Cancel
            </Button>
          </div>
        )}
      </div>
    </div>
  )

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">My Reservations</h1>
          <p className="text-muted-foreground mt-1">
            View and manage your dining reservations
          </p>
        </div>
        <Link href="/dashboard/book">
          <Button className="bg-primary text-primary-foreground hover:bg-primary/90">
            <CalendarPlus className="h-4 w-4 mr-2" />
            New Reservation
          </Button>
        </Link>
      </div>

      {/* Tabs */}
      <Tabs defaultValue="confirmed" className="space-y-6">
        <TabsList className="grid w-full max-w-md grid-cols-3">
          <TabsTrigger value="pending">
            Pending ({reservations.pending.length})
          </TabsTrigger>
          <TabsTrigger value="confirmed">
            Confirmed ({reservations.confirmed.length})
          </TabsTrigger>
          <TabsTrigger value="cancelled">
            Cancelled ({reservations.cancelled.length})
          </TabsTrigger>
        </TabsList>

        <TabsContent value="pending" className="space-y-4">
          {reservations.pending.length > 0 ? (
            reservations.pending.map((reservation) => (
              <ReservationCard key={reservation.id} reservation={reservation} />
            ))
          ) : (
            <Card>
              <CardContent className="py-12 text-center">
                <p className="text-muted-foreground">No pending reservations</p>
              </CardContent>
            </Card>
          )}
        </TabsContent>

        <TabsContent value="confirmed" className="space-y-4">
          {reservations.confirmed.length > 0 ? (
            reservations.confirmed.map((reservation) => (
              <ReservationCard key={reservation.id} reservation={reservation} />
            ))
          ) : (
            <Card>
              <CardContent className="py-12 text-center">
                <p className="text-muted-foreground">No confirmed reservations</p>
              </CardContent>
            </Card>
          )}
        </TabsContent>

        <TabsContent value="cancelled" className="space-y-4">
          {reservations.cancelled.length > 0 ? (
            reservations.cancelled.map((reservation) => (
              <ReservationCard key={reservation.id} reservation={reservation} showActions={false} />
            ))
          ) : (
            <Card>
              <CardContent className="py-12 text-center">
                <p className="text-muted-foreground">No cancelled reservations</p>
              </CardContent>
            </Card>
          )}
        </TabsContent>
      </Tabs>

      {/* Cancel Dialog */}
      <Dialog open={cancelDialogOpen} onOpenChange={setCancelDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Cancel Reservation</DialogTitle>
            <DialogDescription>
              Are you sure you want to cancel this reservation? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          {selectedReservation && (
            <div className="py-4">
              <div className="p-4 bg-muted rounded-sm space-y-2">
                <p className="font-medium text-foreground">{selectedReservation.zone}</p>
                <p className="text-sm text-muted-foreground">
                  {selectedReservation.date} at {selectedReservation.time}
                </p>
                <p className="text-sm text-muted-foreground">
                  {selectedReservation.partySize} guests
                </p>
              </div>
            </div>
          )}
          <DialogFooter>
            <Button variant="outline" onClick={() => setCancelDialogOpen(false)}>
              Keep Reservation
            </Button>
            <Button variant="destructive" onClick={handleConfirmCancel}>
              <X className="h-4 w-4 mr-2" />
              Cancel Reservation
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}

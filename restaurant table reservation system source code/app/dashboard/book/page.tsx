"use client"

import { useState } from "react"
import Image from "next/image"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Calendar } from "@/components/ui/calendar"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { cn } from "@/lib/utils"
import { format, addDays } from "date-fns"
import { CalendarIcon, Users, MapPin, Clock, ArrowRight, Check } from "lucide-react"

const zones = [
  { id: "patio", name: "The Patio", image: "/images/zone-patio.jpg", capacity: "2-8 guests" },
  { id: "dining-room", name: "Main Dining Room", image: "/images/zone-dining.jpg", capacity: "2-8 guests" },
  { id: "bar", name: "The Bar", image: "/images/zone-bar.jpg", capacity: "1-6 guests" },
]

const timeSlots = [
  "5:00 PM", "5:30 PM", "6:00 PM", "6:30 PM", "7:00 PM", "7:30 PM", "8:00 PM", "8:30 PM", "9:00 PM", "9:30 PM"
]

const partySizes = ["1", "2", "3", "4", "5", "6", "7", "8"]

export default function DashboardBookPage() {
  const router = useRouter()
  const [isSubmitting, setIsSubmitting] = useState(false)
  
  const [formData, setFormData] = useState({
    partySize: "",
    zone: "",
    date: undefined as Date | undefined,
    time: "",
    specialRequests: "",
  })

  const canSubmit = formData.partySize && formData.zone && formData.date && formData.time

  const handleSubmit = async () => {
    if (!canSubmit) return
    setIsSubmitting(true)
    // Mock submission
    await new Promise(resolve => setTimeout(resolve, 1500))
    router.push("/dashboard/reservations")
  }

  const selectedZone = zones.find(z => z.id === formData.zone)

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div>
        <h1 className="font-serif text-2xl md:text-3xl text-foreground">Book a Reservation</h1>
        <p className="text-muted-foreground mt-1">
          Select your preferences to reserve a table
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Form */}
        <div className="lg:col-span-2 space-y-6">
          {/* Party Size */}
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg flex items-center gap-2">
                <Users className="h-5 w-5 text-primary" />
                Party Size
              </CardTitle>
            </CardHeader>
            <CardContent>
              <Select value={formData.partySize} onValueChange={(value) => setFormData({ ...formData, partySize: value })}>
                <SelectTrigger className="bg-background max-w-xs">
                  <SelectValue placeholder="Select party size" />
                </SelectTrigger>
                <SelectContent>
                  {partySizes.map((size) => (
                    <SelectItem key={size} value={size}>
                      {size} {parseInt(size) === 1 ? "Guest" : "Guests"}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </CardContent>
          </Card>

          {/* Dining Zone */}
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg flex items-center gap-2">
                <MapPin className="h-5 w-5 text-primary" />
                Dining Zone
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {zones.map((zone) => (
                  <button
                    key={zone.id}
                    onClick={() => setFormData({ ...formData, zone: zone.id })}
                    className={cn(
                      "relative aspect-[4/3] rounded-sm overflow-hidden border-2 transition-all",
                      formData.zone === zone.id
                        ? "border-primary ring-2 ring-primary/20"
                        : "border-transparent hover:border-border"
                    )}
                  >
                    <Image
                      src={zone.image}
                      alt={zone.name}
                      fill
                      className="object-cover"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                    <div className="absolute bottom-0 left-0 right-0 p-3 text-left">
                      <h3 className="font-serif text-sm text-white">{zone.name}</h3>
                      <p className="text-white/70 text-xs">{zone.capacity}</p>
                    </div>
                    {formData.zone === zone.id && (
                      <div className="absolute top-2 right-2 w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                        <Check className="h-3 w-3 text-primary-foreground" />
                      </div>
                    )}
                  </button>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Date & Time */}
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg flex items-center gap-2">
                <CalendarIcon className="h-5 w-5 text-primary" />
                Date & Time
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              <div>
                <Label className="mb-3 block">Select Date</Label>
                <Calendar
                  mode="single"
                  selected={formData.date}
                  onSelect={(date) => setFormData({ ...formData, date })}
                  disabled={(date) => date < new Date() || date > addDays(new Date(), 60)}
                  className="rounded-sm border bg-background"
                />
              </div>

              {formData.date && (
                <div>
                  <Label className="mb-3 block">Select Time</Label>
                  <div className="grid grid-cols-5 gap-2">
                    {timeSlots.map((time) => (
                      <Button
                        key={time}
                        type="button"
                        variant={formData.time === time ? "default" : "outline"}
                        size="sm"
                        onClick={() => setFormData({ ...formData, time })}
                        className={cn(
                          formData.time === time && "bg-primary text-primary-foreground"
                        )}
                      >
                        {time}
                      </Button>
                    ))}
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Special Requests */}
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg">Special Requests</CardTitle>
            </CardHeader>
            <CardContent>
              <Textarea
                value={formData.specialRequests}
                onChange={(e) => setFormData({ ...formData, specialRequests: e.target.value })}
                placeholder="Allergies, special occasions, seating preferences..."
                className="bg-background min-h-[100px]"
              />
            </CardContent>
          </Card>
        </div>

        {/* Summary Sidebar */}
        <div className="lg:col-span-1">
          <div className="sticky top-24">
            <Card>
              <CardHeader>
                <CardTitle className="font-serif text-lg">Reservation Summary</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {formData.partySize && (
                  <div className="flex items-start gap-3">
                    <Users className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="text-sm text-muted-foreground">Party Size</p>
                      <p className="font-medium text-foreground">
                        {formData.partySize} {parseInt(formData.partySize) === 1 ? "Guest" : "Guests"}
                      </p>
                    </div>
                  </div>
                )}

                {selectedZone && (
                  <div className="flex items-start gap-3">
                    <MapPin className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="text-sm text-muted-foreground">Dining Zone</p>
                      <p className="font-medium text-foreground">{selectedZone.name}</p>
                    </div>
                  </div>
                )}

                {formData.date && (
                  <div className="flex items-start gap-3">
                    <CalendarIcon className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="text-sm text-muted-foreground">Date</p>
                      <p className="font-medium text-foreground">
                        {format(formData.date, "EEEE, MMMM d, yyyy")}
                      </p>
                    </div>
                  </div>
                )}

                {formData.time && (
                  <div className="flex items-start gap-3">
                    <Clock className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="text-sm text-muted-foreground">Time</p>
                      <p className="font-medium text-foreground">{formData.time}</p>
                    </div>
                  </div>
                )}

                {!formData.partySize && !formData.zone && !formData.date && (
                  <p className="text-sm text-muted-foreground text-center py-4">
                    Select your preferences to see a summary
                  </p>
                )}

                <Button
                  onClick={handleSubmit}
                  disabled={!canSubmit || isSubmitting}
                  className="w-full bg-primary text-primary-foreground hover:bg-primary/90 mt-4"
                >
                  {isSubmitting ? "Confirming..." : "Confirm Reservation"}
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  )
}
